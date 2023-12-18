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

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Mine;

use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;
use Mine\Traits\ModelMacroTrait;

/**
 * Class MineModel.
 */
class MineModel extends Model
{
    use Cacheable;
    use ModelMacroTrait;

    /**
     * 状态
     */
    public const ENABLE = 1;

    public const DISABLE = 2;

    /**
     * 默认每页记录数.
     */
    public const PAGE_SIZE = 15;

    /**
     * 隐藏的字段列表.
     * @var string[]
     */
    protected array $hidden = ['deleted_at'];

    /**
     * 数据权限字段，表中需要有此字段.
     */
    protected string $dataScopeField = 'created_by';

    /**
     * MineModel constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // 注册常用方法
        $this->registerBase();
        // 注册用户数据权限方法
        $this->registerUserDataScope();
    }

    /**
     * 设置主键的值
     * @param int|string $value
     */
    public function setPrimaryKeyValue($value): void
    {
        $this->{$this->primaryKey} = $value;
    }

    public function getPrimaryKeyType(): string
    {
        return $this->keyType;
    }

    public function save(array $options = []): bool
    {
        return parent::save($options);
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        return parent::update($attributes, $options);
    }

    public function newCollection(array $models = []): MineCollection
    {
        return new MineCollection($models);
    }

    public function getDataScopeField(): string
    {
        return $this->dataScopeField;
    }

    public function setDataScopeField(string $name): self
    {
        $this->dataScopeField = $name;
        return $this;
    }
}
