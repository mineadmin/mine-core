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
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;
use Mine\Annotation\Resubmit;
use Mine\Exception\MineException;
use Mine\Exception\NormalStatusException;
use Mine\MineRequest;
use Mine\Redis\MineLockRedis;

use function Hyperf\Support\make;

/**
 * Class ResubmitAspect.
 */
#[Aspect]
class ResubmitAspect extends AbstractAspect
{
    public array $annotations = [
        Resubmit::class,
    ];

    /**
     * @return mixed
     * @throws Exception
     * @throws \Throwable
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        try {
            /* @var $resubmit Resubmit */
            if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Resubmit::class])) {
                $resubmit = $proceedingJoinPoint->getAnnotationMetadata()->method[Resubmit::class];
            }

            $request = container()->get(MineRequest::class);

            $key = md5(sprintf('%s-%s-%s', $request->ip(), $request->getPathInfo(), $request->getMethod()));

            $lockRedis = new MineLockRedis(
                make(Redis::class),
                make(LoggerFactory::class)->get('Mine Redis Lock')
            );
            $lockRedis->setTypeName('resubmit');

            if ($lockRedis->check($key)) {
                $lockRedis = null;
                throw new NormalStatusException($resubmit->message ?: t('mineadmin.resubmit'), 500);
            }

            $lockRedis->lock($key, $resubmit->second);
            $lockRedis = null;

            return $proceedingJoinPoint->process();
        } catch (\Throwable $e) {
            throw new MineException($e->getMessage(), $e->getCode());
        }
    }
}
