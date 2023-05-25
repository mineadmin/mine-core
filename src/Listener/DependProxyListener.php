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

namespace Mine\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Mine\Annotation\DependProxyCollector;
use Mine\Factory\DependProxyFactory;

#[Listener]
class DependProxyListener implements ListenerInterface
{
    public function listen(): array
    {
        return [ BootApplication::class ];
    }

    public function process(object $event): void
    {
        foreach (DependProxyCollector::list() as $collector) {
            $targets = $collector->values;
            $definition = $collector->provider;
            foreach ($targets as $target) {
                DependProxyFactory::define($target, $definition, true);
            }
        }
    }
}
