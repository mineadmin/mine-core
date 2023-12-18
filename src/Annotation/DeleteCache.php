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
 * 删除缓存。
 */
#[Attribute(Attribute::TARGET_METHOD)]
class DeleteCache extends AbstractAnnotation
{

    /**
     * @param string|null $keys 缓存key，多个以逗号分开
     */
    public function __construct(public ?string $keys = null) {}
}