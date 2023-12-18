<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types = 1);
namespace Mine\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * excel导入导出元数据。
 * @Annotation
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ExcelProperty extends AbstractAnnotation
{
    /**
     * @param string|null $value 字段名
     * @param int|null $index 列索引号
     * @param int|null $width 宽度
     * @param string|null $align 文字对齐方式
     * @param string|null $headColor 表头字体颜色
     * @param string|null $headBgColor 表头背景颜色
     * @param string|null $color 表体文字颜色
     * @param string|null $bgColor 表体表格背景颜色
     * @param array|null $dictData 字典数据
     * @param string|null $dictName 字典名称
     * @param string|null $path 数据路径 用法: object.value
     */
    public function __construct(
        public ?string $value = null,
        public ?int $index = null,
        public ?int $width = null,
        public ?string $align = null,
        public ?string $headColor = null,
        public ?string $headBgColor = null,
        public ?string $color = null,
        public ?string $bgColor = null,
        public ?array $dictData = null,
        public ?string $dictName = null,
        public ?string $path = null
    ) {}
}