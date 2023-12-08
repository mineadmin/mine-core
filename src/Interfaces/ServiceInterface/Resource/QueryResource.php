<?php

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