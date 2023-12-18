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

class BeforeProduce
{
    public $producer;

    public $delayTime;

    public function __construct(ProducerMessageInterface $producer, int $delayTime)
    {
        $this->producer = $producer;
        $this->delayTime = $delayTime;
    }
}
