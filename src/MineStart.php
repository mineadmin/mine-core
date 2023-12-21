<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Mine;

use Composer\InstalledVersions;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Framework\Bootstrap\ServerStartCallback;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function Hyperf\Support\env;

class MineStart extends ServerStartCallback
{
    private StdoutLoggerInterface $stdoutLogger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function beforeStart()
    {
        $console = console();
        $console->info('MineAdmin start success...');
        $console->info($this->welcome());
        str_contains(PHP_OS, 'CYGWIN')
        && $console->info('current booting the user: ' . shell_exec('whoami'));
    }

    protected function welcome(): string
    {
        $projectBasePath = realpath(
            dirname(
                InstalledVersions::getInstallPath('xmo/mine-core'),
                2
            )
        );
        if (
            env('WELCOME_FILE')
            && file_exists(
                $projectBasePath .
                DIRECTORY_SEPARATOR .
                env('WELCOME_FILE')
            )
        ) {
            $welcome = file_get_contents($projectBasePath .
                DIRECTORY_SEPARATOR .
                env('WELCOME_FILE'));
        } else {
            $welcome = '
/---------------------- welcome to use -----------------------\
|               _                ___       __          _      |
|    ____ ___  (_)___  _____    /   | ____/ /___ ___  (_)___  |
|   / __ `__ \/ / __ \/ ___/   / /| |/ __  / __ `__ \/ / __ \ |
|  / / / / / / / / / / /__/   / ___ / /_/ / / / / / / / / / / |
| /_/ /_/ /_/_/_/ /_/\___/   /_/  |_\__,_/_/ /_/ /_/_/_/ /_/  |
|                                                             |
\_____________  Copyright MineAdmin 2021 ~ %y  _____________|
            ';
        }
        return str_replace([
            '%y',
        ], [
            date('Y'),
        ], $welcome);
    }
}
