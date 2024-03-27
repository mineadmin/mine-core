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
use Hyperf\Di\Exception\Exception;
use Mine\Annotation\Auth;
use Mine\Exception\TokenException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AuthAspect.
 */
#[Aspect]
class AuthAspect extends AbstractAspect
{
    public array $annotations = [
        Auth::class,
    ];

    /**
     * @return mixed
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $scene = 'default';

        /* @var $auth Auth */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->class[Auth::class])) {
            $auth = $proceedingJoinPoint->getAnnotationMetadata()->class[Auth::class];
            $scene = $auth->scene ?? 'default';
        }

        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Auth::class])) {
            $auth = $proceedingJoinPoint->getAnnotationMetadata()->method[Auth::class];
            $scene = $auth->scene ?? 'default';
        }

        $loginUser = user($scene);

        if (! $loginUser->check(null, $scene)) {
            throw new TokenException(t('jwt.validate_fail'));
        }

        return $proceedingJoinPoint->process();
    }
}
