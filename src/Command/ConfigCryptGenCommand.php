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
use Mine\Helper\Str;
use Mine\MineCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class JwtCommand.
 */
#[Command]
class ConfigCryptGenCommand extends MineCommand
{
    /**
     * 生成key和向量
     */
    protected ?string $name = 'mine:config-crypt-gen';

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php mine:config-crypt-gen" create the key and iv for config encrypt');
        $this->setDescription('MineAdmin system gen config crypt key and iv command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {

        $key = base64_encode(random_bytes(32));
        $iv = base64_encode(random_bytes(openssl_cipher_iv_length('AES-128-CBC')));

        $this->info('config encrypt key generator successfully:' . $key);
        $this->info('config encrypt iv generator successfully:' . $iv);
    }

}
