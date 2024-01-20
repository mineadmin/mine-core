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

use Hyperf\Database\Model\Builder;

interface QueryResource
{
    /**
     * 获取Query.
     */
    public function getQuery(): Builder;

    /**
     * 处理请求
     */
    public function handleSearch(Builder $query, array $params = [], array $extras = []): Builder;
}
