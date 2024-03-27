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

namespace Mine\Crontab\Mutex;

use Hyperf\Collection\Arr;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Coroutine\Coroutine;
use Hyperf\Redis\RedisFactory;
use Mine\Crontab\MineCrontab;

class RedisServerMutex implements ServerMutex
{
    /**
     * @var RedisFactory
     */
    private $redisFactory;

    /**
     * @var null|string
     */
    private $macAddress;

    public function __construct(RedisFactory $redisFactory)
    {
        $this->redisFactory = $redisFactory;

        $this->macAddress = $this->getMacAddress();
    }

    /**
     * Attempt to obtain a server mutex for the given crontab.
     */
    public function attempt(MineCrontab $crontab): bool
    {
        if ($this->macAddress === null) {
            return false;
        }

        $redis = $this->redisFactory->get($crontab->getMutexPool());
        $mutexName = $this->getMutexName($crontab);

        $result = (bool) $redis->set($mutexName, $this->macAddress, ['NX', 'EX' => $crontab->getMutexExpires()]);

        if ($result === true) {
            Coroutine::create(function () use ($crontab, $redis, $mutexName) {
                $exited = CoordinatorManager::until(Constants::WORKER_EXIT)->yield($crontab->getMutexExpires());
                $exited && $redis->del($mutexName);
            });
            return true;
        }

        return $redis->get($mutexName) === $this->macAddress;
    }

    /**
     * Get the server mutex for the given crontab.
     */
    public function get(MineCrontab $crontab): string
    {
        return (string) $this->redisFactory->get($crontab->getMutexPool())->get(
            $this->getMutexName($crontab)
        );
    }

    protected function getMutexName(MineCrontab $crontab): string
    {
        return 'MineAdmin' . DIRECTORY_SEPARATOR . 'crontab-' . sha1($crontab->getName() . $crontab->getRule()) . '-sv';
    }

    protected function getMacAddress(): ?string
    {
        $macAddresses = swoole_get_local_mac();

        foreach (Arr::wrap($macAddresses) as $name => $address) {
            if ($address && $address !== '00:00:00:00:00:00') {
                return $name . ':' . str_replace(':', '', $address);
            }
        }

        return null;
    }
}
