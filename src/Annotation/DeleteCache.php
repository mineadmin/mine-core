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

namespace Mine\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 删除缓存。
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class DeleteCache extends AbstractAnnotation
{
    /**
     * @param null|string $keys 缓存key，多个以逗号分开
     */
    public function __construct(public ?string $keys = null) {}
}
