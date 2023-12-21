<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Mine\Abstracts;

use Hyperf\Redis\Redis;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractRedis.
 */
abstract class AbstractRedis
{
    protected string $prefix;

    /**
     * key 类型名.
     */
    protected string $typeName;

    /**
     * redis实例.
     */
    protected Redis $redis;

    /**
     * 日志实例.
     */
    protected LoggerInterface $logger;

    public function __construct(
        Redis $redis,
        LoggerInterface $logger
    ) {
        $this->redis = $redis;
        $this->logger = $logger;
        $this->prefix = \Hyperf\Config\config('cache.default.prefix');
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getRedis(): Redis
    {
        return $this->redis;
    }

    /**
     * 获取redis实例.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function redis(): Redis
    {
        return $this->getRedis();
    }

    /**
     * 获取key.
     */
    public function getKey(string $key): ?string
    {
        return empty($key) ? null : ($this->prefix . trim($this->typeName, ':') . ':' . $key);
    }
}
