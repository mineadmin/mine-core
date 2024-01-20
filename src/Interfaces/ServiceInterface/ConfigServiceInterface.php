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

namespace Mine\Interfaces\ServiceInterface;

interface ConfigServiceInterface
{
    /**
     * 按key获取配置，并缓存.
     * @throws \RedisException
     */
    public function getConfigByKey(string $key): ?array;
}
