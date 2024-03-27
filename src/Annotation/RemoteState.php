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
 * 设置某个万能通用接口状态，true 允许使用，false 禁止使用.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class RemoteState extends AbstractAnnotation
{
    /**
     * @param bool $state 状态
     */
    public function __construct(public bool $state = true) {}
}
