<?php

namespace Mine\Interfaces\ServiceInterface\Resource;

/**
 * 基础资源Service
 */
interface BaseResource
{
    public function resource(array $params = [],array $extras = []): array;
}