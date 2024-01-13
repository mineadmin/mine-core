<?php
/**
 * Description:消费切面
 * Created by phpStorm.
 * User: mike
 * Date: 2021/11/19
 * Time: 下午2:14.
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

namespace Mine\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Mine\Amqp\Event\AfterConsume;
use Mine\Amqp\Event\BeforeConsume;
use Mine\Amqp\Event\FailToConsume;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ConsumeAspect.
 */
#[Aspect]
class ConsumeAspect extends AbstractAspect
{
    public array $classes = [
        'Hyperf\Amqp\Message\ConsumerMessage::consumeMessage',
    ];

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $data = $proceedingJoinPoint->getArguments()[0];
        $message = $proceedingJoinPoint->getArguments()[1];
        try {
            event(new BeforeConsume($message, $data));
            $result = $proceedingJoinPoint->process();
            event(new AfterConsume($message, $data, $result));
            return $result;
        } catch (\Throwable $e) {
            event(new FailToConsume($message, $data, $e));
            return null;
        }
    }
}
