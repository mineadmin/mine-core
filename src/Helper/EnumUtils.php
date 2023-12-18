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

namespace Mine\Helper;

use Hyperf\Constants\ConstantsCollector;

class EnumUtils
{
    /**
     * 获取指定的enums原始数据.
     */
    public static function getConstantsBy(string $enumClassName): array
    {
        return ConstantsCollector::get($enumClassName, []);
    }

    /**
     * 获取指定的enums数据。将值和message作为value、label返回二维数组.
     */
    public static function getLabelValue(string $enumClassName): array
    {
        return self::collectConvertLabelValue(
            self::getConstantsBy(
                $enumClassName
            )
        );
    }

    /**
     * 将传入的原始数据转换为value,label二维数组.
     */
    public static function collectConvertLabelValue(array $collectData): array
    {
        $result = [];
        foreach ($collectData as $value => $annotation) {
            $result[] = [
                'label' => $annotation['message'] ?? '--',
                'value' => $value,
            ];
        }
        return $result;
    }

    /**
     * 获取指定的enums。排除指定的值
     */
    public static function exceptConstantData(string $enumsClass, array $values): array
    {
        $constantData = self::getConstantsBy($enumsClass);
        foreach ($constantData as $value => $annotation) {
            if (in_array($value, $values)) {
                unset($constantData[$value]);
            }
        }
        return $constantData;
    }
}
