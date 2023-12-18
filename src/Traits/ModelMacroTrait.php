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

namespace Mine\Traits;

use App\System\Model\SystemDept;
use App\System\Model\SystemRole;
use App\System\Model\SystemUser;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Exception\MineException;
use function Hyperf\Config\config;
use function Hyperf\Support\env;

trait ModelMacroTrait
{
    /**
     * 注册自定义方法.
     */
    private function registerUserDataScope()
    {
        // 数据权限方法
        $model = $this;
        Builder::macro('userDataScope', function (?int $userid = null) use ($model) {
            if (! config('mineadmin.data_scope_enabled')) {
                return $this;
            }

            $userid = is_null($userid) ? (int) user()->getId() : $userid;

            if (empty($userid)) {
                throw new MineException('Data Scope missing user_id');
            }

            /* @var Builder $this */
            if ($userid == env('SUPER_ADMIN')) {
                return $this;
            }

            if (! in_array($model->getDataScopeField(), $model->getFillable())) {
                return $this;
            }

            $dataScope = new class($userid, $this, $model) {
                // 用户ID
                protected int $userid;

                // 查询构造器
                protected Builder $builder;

                // 数据范围用户ID列表
                protected array $userIds = [];

                // 外部模型
                protected mixed $model;

                public function __construct(int $userid, Builder $builder, mixed $model)
                {
                    $this->userid = $userid;
                    $this->builder = $builder;
                    $this->model = $model;
                }

                public function execute(): Builder
                {
                    $this->getUserDataScope();
                    return empty($this->userIds)
                        ? $this->builder
                        : $this->builder->whereIn($this->model->getDataScopeField(), array_unique($this->userIds));
                }

                protected function getUserDataScope(): void
                {
                    $userModel = SystemUser::find($this->userid, ['id']);
                    $roles = $userModel->roles()->get(['id', 'data_scope']);

                    foreach ($roles as $role) {
                        switch ($role->data_scope) {
                            case SystemRole::ALL_SCOPE:
                                // 如果是所有权限，跳出所有循环
                                break 2;
                            case SystemRole::CUSTOM_SCOPE:
                                // 自定义数据权限
                                $deptIds = $role->depts()->pluck('id')->toArray();
                                $this->userIds = array_merge(
                                    $this->userIds,
                                    Db::table('system_user_dept')->whereIn('dept_id', $deptIds)->pluck('user_id')->toArray()
                                );
                                $this->userIds[] = $this->userid;
                                break;
                            case SystemRole::SELF_DEPT_SCOPE:
                                // 本部门数据权限
                                $deptIds = Db::table('system_user_dept')->where('user_id', $userModel->id)->pluck('dept_id')->toArray();
                                $this->userIds = array_merge(
                                    $this->userIds,
                                    Db::table('system_user_dept')->whereIn('dept_id', $deptIds)->pluck('user_id')->toArray()
                                );
                                $this->userIds[] = $this->userid;
                                break;
                            case SystemRole::DEPT_BELOW_SCOPE:
                                // 本部门及子部门数据权限
                                $parentDepts = Db::table('system_user_dept')->where('user_id', $userModel->id)->pluck('dept_id')->toArray();
                                $ids = [];
                                foreach ($parentDepts as $deptId) {
                                    $ids[] = SystemDept::query()
                                        ->where(function ($query) use ($deptId) {
                                            $query->where('id', '=', $deptId)
                                                ->orWhere('level', 'like', $deptId . ',%')
                                                ->orWhere('level', 'like', '%,' . $deptId)
                                                ->orWhere('level', 'like', '%,' . $deptId . ',%');
                                        })
                                        ->pluck('id')
                                        ->toArray();
                                }
                                $deptIds = array_merge($parentDepts, ...$ids);
                                $this->userIds = array_merge(
                                    $this->userIds,
                                    Db::table('system_user_dept')->whereIn('dept_id', $deptIds)->pluck('user_id')->toArray()
                                );
                                $this->userIds[] = $this->userid;
                                break;
                            case SystemRole::DEPT_BELOW_SCOPE_BY_TABLE_DEPTID:
                                $parentDepts = Db::table('system_user_dept')->where('user_id', $userModel->id)->pluck('dept_id')->toArray();
                                $ids = [];
                                foreach ($parentDepts as $deptId) {
                                    $ids[] = SystemDept::query()
                                        ->where(function ($query) use ($deptId) {
                                            $query->where('id', '=', $deptId)
                                                ->orWhere('level', 'like', $deptId . ',%')
                                                ->orWhere('level', 'like', '%,' . $deptId)
                                                ->orWhere('level', 'like', '%,' . $deptId . ',%');
                                        })
                                        ->pluck('id')
                                        ->toArray();
                                }
                                $deptIds = array_merge($parentDepts, ...$ids);

                                // 如果是部门单独处理 数据范围
                                if ($this->model instanceof SystemDept) {
                                    $this->builder = $this->builder->whereIn('id', $deptIds);
                                    break;
                                }

                                // 本部门及子部门数据权限 以 当前表的dept_id作为条件
                                if (! in_array('dept_id', $this->model->getFillable())) {
                                    break;
                                }

                                $this->builder = $this->builder->whereIn('dept_id', $deptIds);
                                // no break
                            case SystemRole::SELF_SCOPE:
                                $this->userIds[] = $this->userid;
                                break;
                            default:
                                break;
                        }
                    }
                }
            };

            return $dataScope->execute();
        });
    }

    /**
     * Description:注册常用自定义方法
     * User:mike.
     */
    private function registerBase()
    {
        // 添加andFilterWhere()方法
        Builder::macro('andFilterWhere', function ($key, $operator, $value = null) {
            if ($value === '' || $value === '%%' || $value === '%') {
                return $this;
            }
            if ($operator === '' || $operator === '%%' || $operator === '%') {
                return $this;
            }
            if ($value === null) {
                return $this->where($key, $operator);
            }
            return $this->where($key, $operator, $value);
        });

        // 添加orFilterWhere()方法
        Builder::macro('orFilterWhere', function ($key, $operator, $value = null) {
            if ($value === '' || $value === '%%' || $value === '%') {
                return $this;
            }
            if ($operator === '' || $operator === '%%' || $operator === '%') {
                return $this;
            }
            if ($value === null) {
                return $this->orWhere($key, $operator);
            }
            return $this->orWhere($key, $operator, $value);
        });
    }
}
