<?php

namespace Mine\Interfaces\ServiceInterface\Resource;

interface ConstResource
{
    public function getConst(array $params = [], array $extras = []): string;
}