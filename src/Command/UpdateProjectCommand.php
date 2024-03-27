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

namespace Mine\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Database\Migrations\Migrator;
use Hyperf\Database\Seeders\Seed;
use Mine\Mine;
use Mine\MineCommand;

use function Hyperf\Support\make;

/**
 * Class UpdateProjectCommand.
 */
#[Command]
class UpdateProjectCommand extends MineCommand
{
    /**
     * 更新项目命令.
     */
    protected ?string $name = 'mine:update';

    protected array $database = [];

    protected Seed $seed;

    protected Migrator $migrator;

    /**
     * UpdateProjectCommand constructor.
     */
    public function __construct(Migrator $migrator, Seed $seed)
    {
        parent::__construct();
        $this->migrator = $migrator;
        $this->seed = $seed;
    }

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php mine:update" Update MineAdmin system');
        $this->setDescription('MineAdmin system update command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $modules = make(Mine::class)->getModuleInfo();
        $basePath = BASE_PATH . '/app/';
        $this->migrator->setConnection('default');

        foreach ($modules as $name => $module) {
            $seedPath = $basePath . $name . '/Database/Seeders/Update';
            $migratePath = $basePath . $name . '/Database/Migrations/Update';

            if (is_dir($migratePath)) {
                $this->migrator->run([$migratePath]);
            }

            if (is_dir($seedPath)) {
                $this->seed->run([$seedPath]);
            }
        }

        redis()->flushDB();

        $this->line($this->getGreenText('updated successfully...'));
    }
}
