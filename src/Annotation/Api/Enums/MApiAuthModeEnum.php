<?php

namespace Mine\Annotation\Api\Enums;

enum MApiAuthModeEnum: int
{
    /**
     * 简单模式
     */
    case EASY = 1;

    /**
     * 复杂模式
     */
    case NORMAL = 2;
}
