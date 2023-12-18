<?php

namespace Mine\Interfaces\ServiceInterface\Resource;

interface FieldValueResource
{
    /**
     * 获取select field.
     */
    public function getField(): string;

    /**
     * 获取select value.
     */
    public function getValue(): string;
}