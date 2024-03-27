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

namespace Mine\Crontab;

use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Swoole\Coroutine;

use function Hyperf\Coroutine\co;

class MineCrontabStrategy
{
    /**
     * MineCrontabManage.
     */
    #[Inject]
    protected MineCrontabManage $mineCrontabManage;

    /**
     * MineExecutor.
     */
    #[Inject]
    protected MineExecutor $executor;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function dispatch(MineCrontab $crontab)
    {
        co(function () use ($crontab) {
            if ($crontab->getExecuteTime() instanceof Carbon) {
                $wait = $crontab->getExecuteTime()->getTimestamp() - time();
                $wait > 0 && Coroutine::sleep($wait);
                $this->executor->execute($crontab);
            }
        });
    }

    /**
     * 执行一次
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function executeOnce(MineCrontab $crontab)
    {
        co(function () use ($crontab) {
            $this->executor->execute($crontab);
        });
    }
}
