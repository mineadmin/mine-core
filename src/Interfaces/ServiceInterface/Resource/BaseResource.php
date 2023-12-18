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

namespace Mine\Interfaces\ServiceInterface\Resource;

/**
 * 基础资源Service.
 */
interface BaseResource
{
    public function resource(array $params = [], array $extras = []): array;
}
