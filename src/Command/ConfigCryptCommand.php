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

namespace Mine\Command;

use Hyperf\Command\Annotation\Command;
use Mine\MineCommand;
use Symfony\Component\Console\Input\InputArgument;

use function Hyperf\Config\config;

/**
 * Class JwtCommand.
 */
#[Command]
class ConfigCryptCommand extends MineCommand
{
    /**
     * 生成JWT密钥命令.
     */
    protected ?string $name = 'mine:config-crypt';

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php mine:config-crypt" encrypt');
        $this->setDescription('MineAdmin system config crypt command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $value = $this->input->getArgument('value');
        $key = config('mineadmin.config_encryption_key', '');
        if (empty($key)) {
            $this->line('Not found mineadmin.config_encryption_key config.', 'error');
            return self::FAILURE;
        }

        $key = @base64_decode($key);
        if (empty($key)) {
            $this->line('key content error.', 'error');
            return self::FAILURE;
        }

        $iv = config('mineadmin.config_encryption_iv', '');
        if (empty($iv)) {
            $this->line('Not found mineadmin.config_encryption_iv config.', 'error');
            return self::FAILURE;
        }

        $iv = @base64_decode($iv);
        if (empty($iv)) {
            $this->line('iv content error.', 'error');
            return self::FAILURE;
        }

        $encrypt = @openssl_encrypt($value, 'AES-128-CBC', $key, 0, $iv);

        if (empty($encrypt)) {
            $this->line('iv or key content error.please regen', 'error');
            return self::FAILURE;
        }

        $this->info('config crypt string is: ENC(' . $encrypt . ')');
    }

    protected function getArguments()
    {
        return [
            ['value', InputArgument::REQUIRED, 'source value'],
        ];
    }
}
