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

namespace Mine\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Support\Filesystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;
use Mine\MineCommand;
use function Hyperf\Config\config;
use function Hyperf\Support\env;
use function Hyperf\Support\make;

/**
 * Class HttpGenCommand.
 */
#[Command]
class HttpGenCommand extends MineCommand
{
    /**
     * 安装命令.
     */
    protected ?string $name = 'mine:http-gen';

    protected string $description = 'gen the http-client';

    public function handle()
    {
        $fileSystem = make(Filesystem::class);
        $outputDir = BASE_PATH . '/runtime/http';
        $fileSystem->exists($outputDir) && $fileSystem->deleteDirectory($outputDir);
        $fileSystem->makeDirectory($outputDir);
        $httpJsonFile = config('api_docs.output_dir') . '/http.json';
        if (! $fileSystem->exists($httpJsonFile)) {
            throw new NormalStatusException('请先生成swagger文档');
        }
        $swagger = json_decode($fileSystem->get($httpJsonFile), true);
        // 生成文件
        $paths = $swagger['paths'];
        foreach ($paths as $path => $methods) {
            $method = array_key_first($methods);
            $fileName = $methods[$method]['tags'][0] . '.http';
            $filePath = $outputDir . '/' . $fileName;
            $content =
                '### ' . $methods[$method]['summary'] . PHP_EOL .
                '// @no-log' . PHP_EOL .
                Str::upper($method) . ' {{host}}' . $path . PHP_EOL .
                'Content-Type: ' . $methods[$method]['produces'][0] . PHP_EOL;
            $content .= 'Authorization: Bearer {{auth_token}}' . PHP_EOL . PHP_EOL;
            $params = [];
            foreach ($methods[$method]['parameters'] as $param) {
                $params = array_merge($params, [$param['name'] => $param['default'] ?? '']);
            }
            if (! empty($params)) {
                $params = str_replace(['{', ',', '}'], ["{\r ", ",\r ", "\r}"], json_encode($params, JSON_UNESCAPED_UNICODE));
                $content .= $params . PHP_EOL . PHP_EOL;
            }
            if ($path === '/system/login') {
                $setToken = '> {% client.global.set("auth_token", response.body.data.token); %}' . PHP_EOL . PHP_EOL;
                $content .= $setToken . PHP_EOL . PHP_EOL;
            }
            $this->genHttpFile($filePath, $content);
        }
        $this->genEnvFile();
        $this->line('http-client generate success!', 'comment');
    }

    /**
     * 生成http文件.
     * @param mixed $file
     * @param mixed $content
     */
    public function genHttpFile($file, $content): void
    {
        $fileSystem = make(Filesystem::class);
        if (! $fileSystem->exists($file)) {
            $fileSystem->put($file, $content);
        } else {
            $fileSystem->append($file, $content);
        }
    }

    /**
     * 生成http-client.env文件.
     */
    public function genEnvFile(): void
    {
        $fileSystem = make(Filesystem::class);
        $host = env('HTTP_HOST', '127.0.0.1:9501');
        $content = <<<JSON
        {
          "dev": {
            "host": "{$host}"
          }
        }
        JSON;
        $fileSystem->put(BASE_PATH . '/runtime/http/http-client.env.json', $content);
    }
}
