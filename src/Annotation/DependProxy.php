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

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 依赖代理注解，用于平替某个类
 */
#[Attribute(Attribute::TARGET_CLASS)]
class DependProxy extends AbstractAnnotation
{
    public function __construct(public array $values = [], public ?string $provider = null){}

    public function collectClass(string $className): void
    {
        if (! $this->provider) {
            $this->provider = $className;
        }
        if (count($this->values) == 0 && class_exists($className)) {
            $reflectionClass = new \ReflectionClass(make($className));
            $this->values = array_keys($reflectionClass->getInterfaces());
        }
        parent::collectClass($className);
        DependProxyCollector::setAround($className, $this);
    }
}
