<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Mine;

use App\System\Mapper\SystemUploadFileMapper;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Snowflake\IdGeneratorInterface;
use League\Flysystem\FileExistsException;
use League\Flysystem\Filesystem;
use Mine\Event\UploadAfter;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;
use Mine\Interfaces\ServiceInterface\ConfigServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

use function Hyperf\Support\env;

class MineUpload
{
    protected FilesystemFactory $factory;

    protected Filesystem $filesystem;

    protected EventDispatcherInterface $evDispatcher;

    protected MineRequest $mineRequest;

    protected IdGeneratorInterface $idGenerator;

    private ConfigServiceInterface $configService;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        FilesystemFactory $factory,
        EventDispatcherInterface $evDispatcher,
        ConfigServiceInterface $configService,
        MineRequest $mineRequest,
        IdGeneratorInterface $idGenerator
    ) {
        $this->factory = $factory;
        $this->evDispatcher = $evDispatcher;
        $this->configService = $configService;
        $this->mineRequest = $mineRequest;
        $this->filesystem = $factory->get($this->getMappingMode());
        $this->idGenerator = $idGenerator;
    }

    /**
     * 获取文件操作处理系统
     */
    public function getFileSystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * 上传文件.
     * @throws FileExistsException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function upload(UploadedFile $uploadedFile, array $config = []): array
    {
        return $this->handleUpload($uploadedFile, $config);
    }

    /**
     * 处理分块上传.
     */
    public function handleChunkUpload(array $data): array
    {
        $uploadFile = $data['package'];
        /* @var UploadedFile $uploadFile */
        $path = BASE_PATH . '/runtime/chunk/';
        $chunkName = "{$path}{$data['hash']}_{$data['total']}_{$data['index']}.chunk";
        $fs = container()->get(\Hyperf\Support\Filesystem\Filesystem::class);
        $fs->isDirectory($path) || $fs->makeDirectory($path);
        $uploadFile->moveTo($chunkName);
        if ($data['index'] === $data['total']) {
            $content = '';
            for ($i = 1; $i <= $data['total']; ++$i) {
                $chunkFile = "{$path}{$data['hash']}_{$data['total']}_{$i}.chunk";
                if (! $fs->isFile($chunkFile)) {
                    return ['chunk' => $data['index'], 'code' => 500, 'status' => 'fail'];
                }
                $content .= $fs->get($chunkFile);
                $fs->delete($chunkFile);
            }
            $fileName = $this->getNewName() . '.' . Str::lower($data['ext']);
            $storagePath = $this->getPath(null, $this->getStorageMode() != 1);
            try {
                $this->filesystem->write($storagePath . '/' . $fileName, $content);
            } catch (\Exception $e) {
                throw new NormalStatusException('分块上传失败：' . $e->getMessage(), 500);
            }
            $fileInfo = [
                'storage_mode' => $this->getStorageMode(),
                'origin_name' => $data['name'],
                'object_name' => $fileName,
                'mime_type' => $data['type'],
                'storage_path' => $storagePath,
                'hash' => $data['hash'],
                'suffix' => $data['ext'],
                'size_byte' => $data['size'],
                'size_info' => format_size((int) $data['size'] * 1024),
                'url' => $this->assembleUrl(null, $fileName),
            ];

            $this->evDispatcher->dispatch(new UploadAfter($fileInfo));

            return $fileInfo;
        }
        return ['chunk' => $data['index'], 'code' => 201, 'status' => 'success'];
    }

    /**
     * 保存网络图片.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function handleSaveNetworkImage(array $data): array
    {
        $path = $this->getPath($data['path'] ?? null, $this->getStorageMode() != 1);
        $filename = $this->getNewName() . '.jpg';

        try {
            if (preg_match('/^\/\//', $data['url'])) {
                $data['url'] = 'http:' . $data['url'];
            }
            if (! preg_match('/^(http|https):\/\//i', $data['url'])) {
                throw new NormalStatusException('图片地址请以 http 或 https 开头', 500);
            }
            $content = file_get_contents($data['url']);

            $handle = fopen($data['url'], 'rb');
            $meta = stream_get_meta_data($handle);
            fclose($handle);

            $dataInfo = $meta['wrapper_data']['headers'] ?? $meta['wrapper_data'];
            $size = 0;

            foreach ($dataInfo as $va) {
                if (preg_match('/length/iU', $va)) {
                    $ts = explode(':', $va);
                    $size = intval(trim(array_pop($ts)));
                    break;
                }
            }

            $realPath = BASE_PATH . '/runtime/' . $filename;
            $fs = container()->get(\Hyperf\Support\Filesystem\Filesystem::class);
            $fs->put($realPath, $content);

            $hash = md5_file($realPath);
            $fs->delete($realPath);

            if (! $hash) {
                throw new \Exception(t('network_image_save_fail'));
            }

            /*
             * TODO 这里回头做优化，单独拆出来一个upload组件
             * @phpstan-ignore-next-line
             */
            if ($model = (new SystemUploadFileMapper())->getFileInfoByHash($hash)) {
                return $model->toArray();
            }

            try {
                $this->filesystem->write($path . '/' . $filename, $content);
            } catch (\Exception $e) {
                throw new \Exception(t('network_image_save_fail'));
            }
        } catch (\Throwable $e) {
            throw new NormalStatusException($e->getMessage(), 500);
        }

        $fileInfo = [
            'storage_mode' => $this->getStorageMode(),
            'origin_name' => md5((string) time()) . '.jpg',
            'object_name' => $filename,
            'mime_type' => 'image/jpg',
            'storage_path' => $path,
            'suffix' => 'jpg',
            'hash' => $hash,
            'size_byte' => $size,
            'size_info' => format_size($size * 1024),
            'url' => $this->assembleUrl($data['path'] ?? null, $filename),
        ];

        $this->evDispatcher->dispatch(new UploadAfter($fileInfo));

        return $fileInfo;
    }

    /**
     * 创建目录.
     */
    public function createUploadDir(string $name): bool
    {
        return $this->filesystem->createDir($name);
    }

    /**
     * 获取目录内容.
     */
    public function listContents(string $path = ''): array
    {
        return $this->filesystem->listContents($path);
    }

    /**
     * 获取目录.
     */
    public function getDirectory(string $path, bool $isChildren): array
    {
        $contents = $this->filesystem->listContents($path, $isChildren);
        $dirs = [];
        foreach ($contents as $content) {
            if ($content['type'] == 'dir') {
                $dirs[] = $content;
            }
        }
        return $dirs;
    }

    /**
     * 组装url.
     */
    public function assembleUrl(?string $path, string $filename): string
    {
        return $this->getPath($path, true) . '/' . $filename;
    }

    /**
     * 获取存储方式.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    public function getStorageMode(): int|string
    {
        return $this->configService->getConfigByKey('upload_mode')['value'] ?? 1;
    }

    /**
     * 获取编码后的文件名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getNewName(): string
    {
        return (string) $this->idGenerator->generate();
    }

    /**
     * 处理上传.
     * @throws FileExistsException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function handleUpload(UploadedFile $uploadedFile, array $config): array
    {
        $tmpFile = $uploadedFile->getPath() . '/' . $uploadedFile->getFilename();
        $path = $this->getPath($config['path'] ?? null, $this->getStorageMode() != 1);
        $filename = $this->getNewName() . '.' . Str::lower($uploadedFile->getExtension());

        try {
            $this->filesystem->writeStream($path . '/' . $filename, $uploadedFile->getStream()->detach());
        } catch (\Exception $e) {
            throw new NormalStatusException((string) $e->getMessage(), 500);
        }

        $fileInfo = [
            'storage_mode' => $this->getStorageMode(),
            'origin_name' => $uploadedFile->getClientFilename(),
            'object_name' => $filename,
            'mime_type' => $uploadedFile->getClientMediaType(),
            'storage_path' => $path,
            'hash' => md5_file($tmpFile),
            'suffix' => Str::lower($uploadedFile->getExtension()),
            'size_byte' => $uploadedFile->getSize(),
            'size_info' => format_size($uploadedFile->getSize() * 1024),
            'url' => $this->assembleUrl($config['path'] ?? null, $filename),
        ];

        $this->evDispatcher->dispatch(new UploadAfter($fileInfo));

        return $fileInfo;
    }

    /**
     * @param false $isContainRoot
     */
    protected function getPath(?string $path = null, bool $isContainRoot = false): string
    {
        $uploadfile = $isContainRoot ? '/' . env('UPLOAD_PATH', 'uploadfile') . '/' : '';
        return empty($path) ? $uploadfile . date('Ymd') : $uploadfile . $path;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getMappingMode(): string
    {
        return match ($this->getStorageMode()) {
            '1' => 'local',
            '2' => 'oss',
            '3' => 'qiniu',
            '4' => 'cos',
            '5' => 'ftp',
            '6' => 'memory',
            '7' => 's3',
            '8' => 'minio',
            default => 'local',
        };
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getProtocol(): string
    {
        return $this->mineRequest->getScheme();
    }
}
