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
use Mine\Annotation\Permission;
use Mine\Exception\NoPermissionException;
use Mine\Helper\LoginUser;
use Mine\Interfaces\ServiceInterface\MenuServiceInterface;
use Mine\Interfaces\ServiceInterface\UserServiceInterface;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class PermissionAspect.
 */
#[Aspect]
class PermissionAspect extends AbstractAspect
{
    public array $annotations = [
        Permission::class,
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
     * PermissionAspect constructor.
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
        if ($this->loginUser->isSuperAdmin()) {
            return $proceedingJoinPoint->process();
        }

        /* @var Permission $permission */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class])) {
            $permission = $proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class];
        }

        // 注解权限为空，则放行
        if (empty($permission->code)) {
            return $proceedingJoinPoint->process();
        }

        $this->checkPermission($permission->code, $permission->where);

        return $proceedingJoinPoint->process();
    }

    /**
     * 检查权限.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function checkPermission(string $codeString, string $where): bool
    {
        $codes = $this->service->getInfo()['codes'];

        if (preg_match_all('#{(.*?)}#U', $codeString, $matches)) {
            if (isset($matches[1])) {
                foreach ($matches[1] as $name) {
                    $codeString = str_replace('{' . $name . '}', $this->request->route($name), $codeString);
                }
            }
        }

        if ($where === 'OR') {
            foreach (explode(',', $codeString) as $code) {
                if (in_array(trim($code), $codes)) {
                    return true;
                }
            }
            throw new NoPermissionException(
                t('system.no_permission') . ' -> [ ' . $this->request->getPathInfo() . ' ]'
            );
        }

        if ($where === 'AND') {
            foreach (explode(',', $codeString) as $code) {
                $code = trim($code);
                if (! in_array($code, $codes)) {
                    $service = container()->get(MenuServiceInterface::class);
                    throw new NoPermissionException(
                        t('system.no_permission') . ' -> [ ' . $service->findNameByCode($code) . ' ]'
                    );
                }
            }
        }

        return true;
    }
}
