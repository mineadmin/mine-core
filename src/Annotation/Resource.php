<?php

namespace Mine\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS)]
class Resource extends AbstractAnnotation
{
    public function __construct(public string $tag){}

    public function collectClass(string $className): void
    {
        ResourceCollector::collectClass($className,$this->tag);
    }
}