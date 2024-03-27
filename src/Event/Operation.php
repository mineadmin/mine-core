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

namespace Mine\Event;

class Operation
{
    protected array $requestInfo;

    public function __construct(array $requestInfo)
    {
        $this->requestInfo = $requestInfo;
    }

    public function getRequestInfo(): array
    {
        return $this->requestInfo;
    }
}
