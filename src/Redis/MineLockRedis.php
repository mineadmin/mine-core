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

namespace Mine\Redis;

use Hyperf\Coroutine\Coroutine;
use Mine\Abstracts\AbstractRedis;
use Mine\Exception\NormalStatusException;
use Mine\Interfaces\MineRedisInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MineLockRedis extends AbstractRedis implements MineRedisInterface
{
    /**
     * 设置 key 类型名.
     */
    public function setTypeName(string $typeName): void
    {
        $this->typeName = $typeName;
    }

    /**
     * 获取key 类型名.
     */
    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /**
     * 运行锁，简单封装.
     * @throws \Throwable
     */
    public function run(\Closure $closure, string $key, int $expired, int $timeout = 0, float $sleep = 0.1): bool
    {
        if (! $this->lock($key, $expired, $timeout, $sleep)) {
            return false;
        }

        /*
         * @phpstan-ignore-next-line
         */
        try {
            \Hyperf\Support\call($closure);
        } catch (\Throwable $e) {
            $this->getLogger()->error(t('mineadmin.redis_lock_error'), [$e->getMessage(), $e->getTrace()]);
            throw new NormalStatusException(t('mineadmin.redis_lock_error'), 500);
        } finally {
            $this->freed($key);
        }

        return true;
    }

    /**
     * 检查锁
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function check(string $key): bool
    {
        return $this->getRedis()->exists($this->getKey($key));
    }

    /**
     * 添加锁
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function lock(string $key, int $expired, int $timeout = 0, float $sleep = 0.1): bool
    {
        $retry = $timeout > 0 ? intdiv($timeout * 100, 10) : 1;

        $name = $this->getKey($key);

        while ($retry > 0) {
            $lock = $this->getRedis()->set($name, 1, ['nx', 'ex' => $expired]);
            if ($lock || $timeout === 0) {
                break;
            }
            Coroutine::id() ? Coroutine::sleep($sleep) : usleep(9999999);

            --$retry;
        }

        return true;
    }

    /**
     * 释放锁
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function freed(string $key): bool
    {
        $luaScript = <<<'Lua'
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        Lua;

        return $this->getRedis()->eval($luaScript, [$this->getKey($key), 1], 1) > 0;
    }
}
