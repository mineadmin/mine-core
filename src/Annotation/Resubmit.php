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
 * 禁止重复提交
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Resubmit extends AbstractAnnotation
{
    /**
     * @var int $second 限制时间（秒）
     * @var string|null $message 提示信息
     */
    public function __construct(public int $second = 3, public ?string $message = null) {}
}