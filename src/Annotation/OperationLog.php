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
 * 记录操作日志注解。
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class OperationLog extends AbstractAnnotation
{
    /**
     * 菜单名称.
     * @var null|string
     */
    public function __construct(public ?string $menuName = null) {}
}
