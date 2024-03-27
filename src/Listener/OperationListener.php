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

namespace Mine\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\Event\Operation;
use Mine\Interfaces\ServiceInterface\OperLogServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class OperationListener.
 */
#[Listener]
class OperationListener implements ListenerInterface
{
    protected $container;

    protected $ignoreRouter = ['/login', '/getInfo', '/system/captcha'];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [Operation::class];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(object $event): void
    {
        $requestInfo = $event->getRequestInfo();
        if (! in_array($requestInfo['router'], $this->ignoreRouter)) {
            $service = $this->container->get(OperLogServiceInterface::class);
            $requestInfo['request_data'] = json_encode($requestInfo['request_data'], JSON_UNESCAPED_UNICODE);
            //            $requestInfo['response_data'] = $requestInfo['response_data'];
            $service->save($requestInfo);
        }
    }
}
