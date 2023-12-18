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

class AfterConsume
{
    /**
     * @var ConsumerMessageInterface
     */
    public $message;

    public $data;

    public $result;

    public function __construct($message, $data, $result)
    {
        $this->message = $message;
        $this->data = $data;
        $this->result = $result;
    }
}
