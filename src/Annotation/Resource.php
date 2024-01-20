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

namespace Mine\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Resource extends AbstractAnnotation
{
    public function __construct(public string $tag) {}

    public function collectClass(string $className): void
    {
        ResourceCollector::collectClass($className, $this->tag);
    }
}
