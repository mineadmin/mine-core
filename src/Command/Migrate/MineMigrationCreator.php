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

namespace Mine\Command\Migrate;

use Hyperf\Database\Migrations\MigrationCreator;

class MineMigrationCreator extends MigrationCreator
{
    public function stubPath(): string
    {
        return BASE_PATH . '/vendor/xmo/mine-core/src/Command/Migrate/Stubs';
    }
}
