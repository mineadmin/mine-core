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
 * 禁止重复提交.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Resubmit extends AbstractAnnotation
{
    /**
     * @var int 限制时间（秒）
     * @var null|string 提示信息
     */
    public function __construct(public int $second = 3, public ?string $message = null) {}
}
