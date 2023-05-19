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

namespace Mine\Annotation;

use Hyperf\Di\MetadataCollector;

/**
 * 依赖代理收集器
 */
class DependProxyCollector extends MetadataCollector
{
    protected static array $container = [];

    public static function setAround(string $class, $value): void
    {
        static::$container[$class] = $value;
    }
}
