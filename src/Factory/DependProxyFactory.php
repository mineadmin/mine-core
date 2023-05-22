<?php

declare(strict_types=1);

/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author @小小只^v^ <littlezov@qq.com>, X.Mo<root@imoi.cn> 
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace Mine\Factory;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Contract\StdoutLoggerInterface;

use Hyperf\Logger\LoggerFactory;
use container;
use function class_exists;
use function interface_exists;

class DependProxyFactory
{
    public static function define(string $name, string $definition, bool $isLogger = true): void
    {
        /** @var ContainerInterface $container */
        $container = ApplicationContext::getContainer();
        $config = $container->get(ConfigInterface::class);

        if (interface_exists($definition) || class_exists($definition)) {
            $config->set("dependencies.{$name}", $definition);
            $container->define($name, $definition);
        }
        if (interface_exists($name)) {
            $config->set("mineadmin.dependProxy.{$name}", $definition);
        }

         if ($container->has($name)) {
             $isLogger && logger()->info(
                 sprintf('Dependencies [%s] Injection to the [%s] successfully.', $definition, $name)
             );
         } else {
             $isLogger && logger()->warning(sprintf('Dependencies [%s] Injection to the [%s] failed.', $definition, $name));
         }
    }
}
