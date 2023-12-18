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
namespace Mine\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Mine\Annotation\RemoteState;
use Mine\Exception\MineException;

/**
 * Class RemoteStateAspect
 * @package Mine\Aspect
 */
#[Aspect]
class RemoteStateAspect extends AbstractAspect
{

    public array $annotations = [
        RemoteState::class
    ];

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
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