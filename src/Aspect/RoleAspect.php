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

namespace Mine\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Mine\Annotation\Role;
use Mine\Exception\NoPermissionException;
use Mine\Helper\LoginUser;
use Mine\Interfaces\ServiceInterface\RoleServiceInterface;
use Mine\Interfaces\ServiceInterface\UserServiceInterface;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class RoleAspect.
 */
#[Aspect]
class RoleAspect extends AbstractAspect
{
    public array $annotations = [
        Role::class,
    ];

    /**
     * UserServiceInterface.
     */
    protected UserServiceInterface $service;

    /**
     * MineRequest.
     */
    protected MineRequest $request;

    /**
     * LoginUser.
     */
    protected LoginUser $loginUser;

    /**
     * RoleAspect constructor.
     */
    public function __construct(
        UserServiceInterface $service,
        MineRequest $request,
        LoginUser $loginUser
    ) {
        $this->service = $service;
        $this->request = $request;
        $this->loginUser = $loginUser;
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 是超管角色放行
        if ($this->loginUser->isAdminRole()) {
            return $proceedingJoinPoint->process();
        }

        /* @var Role $role */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Role::class])) {
            $role = $proceedingJoinPoint->getAnnotationMetadata()->method[Role::class];
        }

        // 没有使用注解，则放行
        if (empty($role->code)) {
            return $proceedingJoinPoint->process();
        }

        $this->checkRole($role->code, $role->where);

        return $proceedingJoinPoint->process();
    }

    /**
     * 检查角色.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function checkRole(string $codeString, string $where): bool
    {
        $roles = $this->service->getInfo()['roles'];

        if ($where === 'OR') {
            foreach (explode(',', $codeString) as $code) {
                if (in_array(trim($code), $roles)) {
                    return true;
                }
            }
            throw new NoPermissionException(
                t('system.no_role') . ' -> [ ' . $codeString . ' ]'
            );
        }

        if ($where === 'AND') {
            foreach (explode(',', $codeString) as $code) {
                $code = trim($code);
                if (! in_array($code, $roles)) {
                    $service = container()->get(RoleServiceInterface::class);
                    throw new NoPermissionException(
                        t('system.no_role') . ' -> [ ' . $service->findNameByCode($code) . ' ]'
                    );
                }
            }
        }

        return true;
    }
}
