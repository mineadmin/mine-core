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

namespace Mine\Amqp\Event;

use Hyperf\Amqp\Message\ProducerMessageInterface;

class FailToProduce extends ConsumeEvent
{
    /**
     * @var \Throwable
     */
    public $throwable;

    public function __construct(ProducerMessageInterface $producer, \Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }
}
