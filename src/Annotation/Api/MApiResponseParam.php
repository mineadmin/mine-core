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

namespace Mine\Annotation\Api;

use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class MApiResponseParam extends AbstractAnnotation
{
    public function __construct(
        // 参数名称
        public string $name,
        // 参数描述
        public string $description,
        // 参数类型  String, Integer, Array, Float, Boolean, Enum, Object, File
        public string $dataType = 'String',
        // 默认值
        public string $defaultValue = '',
        // 是否必须填 1 非必填 2 必填
        public int $isRequired = 1,
        // 是否启用 1 启用 2 不启用
        public int $status = 1,
    ) {}

    public function collectMethod(string $className, ?string $target): void
    {
        MApiResponseParamCollector::collectMethod($className, $target, static::class, $this);
    }
}
