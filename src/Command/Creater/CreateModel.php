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

namespace Mine\Command\Creater;

use Hyperf\Command\Annotation\Command;
use Hyperf\DbConnection\Db;
use Mine\Mine;
use Mine\MineCommand;
use Symfony\Component\Console\Input\InputOption;

use function Hyperf\Support\env;
use function Hyperf\Support\make;

/**
 * Class CreateModel.
 */
#[Command]
class CreateModel extends MineCommand
{
    protected ?string $name = 'mine:model-gen';

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php mine:model-gen <--module | -M <module>> [--table | -T [table]]"');
        $this->setDescription('Generate model to module according to data table');
    }

    public function handle()
    {
        $mine = make(Mine::class);
        $module = $this->input->getOption('module');
        if ($module) {
            $module = ucfirst(trim($this->input->getOption('module')));
        }

        $table = $this->input->getOption('table');
        if ($table) {
            $table = env('DB_PREFIX') . trim($this->input->getOption('table'));
        }

        if (empty($module)) {
            $this->line('Missing parameter <--module < module_name>>', 'error');
        }

        $moduleInfos = $mine->getModuleInfo();

        if (isset($moduleInfos[$module])) {
            $path = "app/{$module}/Model";

            $db = env('DB_DATABASE');
            $prefix = env('DB_PREFIX');

            $tables = Db::select('SHOW TABLES');
            $key = "Tables_in_{$db}";

            $tableList = [];
            foreach ($tables as $k) {
                $tmp = $k->{$key};
                if (! empty($prefix) && preg_match(sprintf('/%s_%s[_a-zA-Z0-9]+/i', $prefix, $module), $tmp)) {
                    $tableList[] = $tmp;
                }
                if (preg_match(sprintf('/%s(\\b|_[a-zA-Z0-9]+)/i', $module), $tmp)) {
                    $tableList[] = $tmp;
                }
            }

            if (! empty($table)) {
                if (! in_array($table, $tableList)) {
                    $this->confirm("Table \"{$table}\" does not exist or not belong to the \"{$module}\" module. Are you sure to generate the model?", false)
                    && $this->call('gen:model', ['table' => $table, '--path' => $path]);
                } else {
                    $this->call('gen:model', ['table' => $table, '--path' => $path]);
                }
            } else {
                foreach ($tableList as $table) {
                    $this->call('gen:model', ['table' => $table, '--path' => $path]);
                }
            }
        }
    }

    protected function getOptions(): array
    {
        return [
            ['module', '-M', InputOption::VALUE_REQUIRED, 'Please enter the module to be generated'],
            ['table', '-T', InputOption::VALUE_OPTIONAL, 'Which table you want to associated with the Model.'],
        ];
    }
}
