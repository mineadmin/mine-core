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

namespace Mine\Abstracts;

use Hyperf\Context\Context;
use Mine\Traits\ServiceTrait;

abstract class AbstractService
{
    use ServiceTrait;

    public $mapper;

    /**
     * 魔术方法，从类属性里获取数据.
     * @param mixed $name
     * @return mixed|string
     */
    public function __get($name)
    {
        return $this->getAttributes()[$name] ?? '';
    }

    /**
     * 把数据设置为类属性.
     */
    public function setAttributes(array $data)
    {
        Context::set('attributes', $data);
    }

    /**
     * 获取数据.
     */
    public function getAttributes(): array
    {
        return Context::get('attributes', []);
    }
}
