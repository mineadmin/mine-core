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
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\Event\Operation;
use Mine\Helper\Ip2region;
use Mine\Helper\LoginUser;
use Mine\Interfaces\ServiceInterface\MenuServiceInterface;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class OperationLogAspect.
 */
#[Aspect]
class OperationLogAspect extends AbstractAspect
{
    public array $annotations = [
        OperationLog::class,
    ];

    /**
     * 容器.
     */
    protected ContainerInterface $container;

    protected Ip2region $ip2region;

    public function __construct()
    {
        $this->container = container();
        $this->ip2region = $this->container->get(Ip2region::class);
    }

    /**
     * @return mixed|void
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $annotation = $proceedingJoinPoint->getAnnotationMetadata()->method[OperationLog::class];
        /* @var $result ResponseInterface */
        $result = $proceedingJoinPoint->process();
        $isDownload = false;
        if (! empty($annotation->menuName) || ($annotation = $proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class])) {
            if (! empty($result->getHeader('content-description')) && ! empty($result->getHeader('content-transfer-encoding'))) {
                $isDownload = true;
            }
            $evDispatcher = $this->container->get(EventDispatcherInterface::class);
            $evDispatcher->dispatch(new Operation($this->getRequestInfo([
                'code' => ! empty($annotation->code) ? explode(',', $annotation->code)[0] : '',
                'name' => $annotation->menuName ?? '',
                'response_code' => $result->getStatusCode(),
                'response_data' => $isDownload ? '文件下载' : $result->getBody()->getContents(),
            ])));
        }
        return $result;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getRequestInfo(array $data): array
    {
        $request = $this->container->get(MineRequest::class);
        $loginUser = $this->container->get(LoginUser::class);

        $operationLog = [
            'time' => date('Y-m-d H:i:s', $request->getServerParams()['request_time']),
            'method' => $request->getServerParams()['request_method'],
            'router' => $request->getServerParams()['path_info'],
            'protocol' => $request->getServerParams()['server_protocol'],
            'ip' => $request->ip(),
            'ip_location' => $this->ip2region->search($request->ip()),
            'service_name' => $data['name'] ?: $this->getOperationMenuName($data['code']),
            'request_data' => $request->all(),
            'response_code' => $data['response_code'],
            'response_data' => $data['response_data'],
        ];
        try {
            $operationLog['username'] = $loginUser->getUsername();
        } catch (\Exception $e) {
            $operationLog['username'] = t('system.no_login_user');
        }

        return $operationLog;
    }

    /**
     * 获取菜单名称.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getOperationMenuName(string $code): string
    {
        return $this->container->get(MenuServiceInterface::class)->findNameByCode($code);
    }
}
