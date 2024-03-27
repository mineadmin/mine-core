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
 * 数据库事务注解。
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Transaction extends AbstractAnnotation
{
    /**
     * @param int $retry 重试次数
     */
    public function __construct(public int $retry = 1, public ?string $connection = null) {}
}
