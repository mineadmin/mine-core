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

namespace Mine\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Mine\Annotation\RemoteState;
use Mine\Exception\MineException;

/**
 * Class RemoteStateAspect.
 */
#[Aspect]
class RemoteStateAspect extends AbstractAspect
{
    public array $annotations = [
        RemoteState::class,
    ];

    /**
     * @return mixed
     * @throws MineException
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $remote = $proceedingJoinPoint->getAnnotationMetadata()->method[RemoteState::class];
        if (! $remote->state) {
            throw new MineException('当前功能服务已禁止使用远程通用接口', 500);
        }

        return $proceedingJoinPoint->process();
    }
}
