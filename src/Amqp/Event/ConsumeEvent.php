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

use Hyperf\Amqp\Message\ConsumerMessageInterface;

class ConsumeEvent
{
    /**
     * @var ConsumerMessageInterface
     */
    public $message;

    public $data;

    public function __construct(ConsumerMessageInterface $message, $data)
    {
        $this->message = $message;
        $this->data = $data;
    }
}
