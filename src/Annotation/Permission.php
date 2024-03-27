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
 * 用户权限验证。
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Permission extends AbstractAnnotation
{
    /**
     * @var null|string 菜单代码
     * @var string 过滤条件 为 OR 时，检查有一个通过则全部通过 为 AND 时，检查有一个不通过则全不通过
     */
    public function __construct(public ?string $code = null, public string $where = 'OR') {}
}
