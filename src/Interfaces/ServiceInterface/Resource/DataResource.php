<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Mine\Interfaces\ServiceInterface\Resource;

interface DataResource
{
    /**
     * 获取数据.
     */
    public function data(array $params = [], array $extras = []): array;
}
