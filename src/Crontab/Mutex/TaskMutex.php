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

namespace Mine\Crontab\Mutex;

use Mine\Crontab\MineCrontab;

interface TaskMutex
{
    /**
     * Attempt to obtain a task mutex for the given crontab.
     */
    public function create(MineCrontab $crontab): bool;

    /**
     * Determine if a task mutex exists for the given crontab.
     */
    public function exists(MineCrontab $crontab): bool;

    /**
     * Clear the task mutex for the given crontab.
     */
    public function remove(MineCrontab $crontab);
}
