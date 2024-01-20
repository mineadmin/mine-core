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

namespace Mine\Annotation\Api\Enums;

enum MApiAuthModeEnum: int
{
    /**
     * 简单模式.
     */
    case EASY = 1;

    /**
     * 复杂模式.
     */
    case NORMAL = 2;
}
