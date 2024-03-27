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

use Hyperf\Support\Filesystem\Filesystem;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Mine
{
    private static string $mineName = 'MineAdmin';

    private static string $version = '2.0.0-dev';

    private string $appPath = '';

    private array $moduleInfo = [];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->setAppPath(BASE_PATH . '/app');
        $this->scanModule();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function scanModule(): void
    {
        $modules = glob(self::getAppPath() . '*');
        $fs = container()->get(Filesystem::class);
        $infos = [];
        foreach ($modules as &$mod) {
            if (is_dir($mod)) {
                $modInfo = $mod . DIRECTORY_SEPARATOR . 'config.json';
                if (file_exists($modInfo)) {
                    $infos[basename($mod)] = json_decode($fs->sharedGet($modInfo), true);
                }
            }
        }
        $sortId = array_column($infos, 'order');
        array_multisort($sortId, SORT_ASC, $infos);
        $this->setModuleInfo($infos);
    }

    public static function getVersion(): string
    {
        return self::$version;
    }

    public static function getMineName(): string
    {
        return self::$mineName;
    }

    /**
     * @return mixed
     */
    public function getAppPath(): string
    {
        return $this->appPath . DIRECTORY_SEPARATOR;
    }

    /**
     * @param mixed $appPath
     */
    public function setAppPath(string $appPath): void
    {
        $this->appPath = $appPath;
    }

    /**
     * @return mixed
     */
    public function getModuleInfo(?string $name = null): array
    {
        if (empty($name)) {
            return $this->moduleInfo;
        }
        return $this->moduleInfo[$name] ?? [];
    }

    /**
     * @param mixed $moduleInfo
     */
    public function setModuleInfo($moduleInfo): void
    {
        $this->moduleInfo = $moduleInfo;
    }

    /**
     * @param false $save
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setModuleConfigValue(string $key, string $value, bool $save = false): bool
    {
        if (strpos($key, '.') > 0) {
            [$mod, $name] = explode('.', $key);
            if (isset($this->moduleInfo[$mod], $this->moduleInfo[$mod][$name])) {
                $this->moduleInfo[$mod][$name] = $value;
                $save && $this->saveModuleConfig($mod);
                return true;
            }
        }
        return false;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function saveModuleConfig(string $mod): void
    {
        if (! empty($mod)) {
            $fs = container()->get(Filesystem::class);
            $modJson = $this->getAppPath() . $mod . DIRECTORY_SEPARATOR . 'config.json';
            if (! $fs->isWritable($modJson)) {
                $fs->chmod($modJson, 666);
            }
            $fs->put($modJson, \json_encode($this->getModuleInfo($mod), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }
}
