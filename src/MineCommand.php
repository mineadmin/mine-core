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

namespace Mine;

use Hyperf\Command\Command as HyperfCommand;

/**
 * Class MineCommand.
 */
abstract class MineCommand extends HyperfCommand
{
    protected const CONSOLE_GREEN_BEGIN = "\033[32;5;1m";

    protected const CONSOLE_RED_BEGIN = "\033[31;5;1m";

    protected const CONSOLE_END = "\033[0m";

    protected string $module;

    protected function getGreenText($text): string
    {
        return self::CONSOLE_GREEN_BEGIN . $text . self::CONSOLE_END;
    }

    protected function getRedText($text): string
    {
        return self::CONSOLE_RED_BEGIN . $text . self::CONSOLE_END;
    }

    protected function getStub($filename): string
    {
        return BASE_PATH . '/vendor/xmo/mine-core/src/Command/Creater/Stubs/' . $filename . '.stub';
    }

    protected function getModulePath(): string
    {
        return BASE_PATH . '/app/' . $this->module . '/Request/';
    }

    protected function getInfo(): string
    {
        return sprintf('
/---------------------- welcome to use -----------------------\
|               _                ___       __          _      |
|    ____ ___  (_)___  _____    /   | ____/ /___ ___  (_)___  |
|   / __ `__ \/ / __ \/ ___/   / /| |/ __  / __ `__ \/ / __ \ |
|  / / / / / / / / / / /__/   / ___ / /_/ / / / / / / / / / / |
| /_/ /_/ /_/_/_/ /_/\___/   /_/  |_\__,_/_/ /_/ /_/_/_/ /_/  |
|                                                             |
\_____________  Copyright MineAdmin 2021 ~ %s  _____________|
', date('Y'));
    }
}
