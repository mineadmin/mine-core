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

use Mine\MineRequest;
use Mine\MineResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

trait ControllerTrait
{
    abstract public function getRequest(): MineRequest;

    abstract public function getResponse(): MineResponse;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function success(null|array|object|string $msgOrData = '', array|object $data = [], int $code = 200): ResponseInterface
    {
        if (is_string($msgOrData) || is_null($msgOrData)) {
            return $this->getResponse()->success($msgOrData, $data, $code);
        }
        if (is_array($msgOrData) || is_object($msgOrData)) {
            return $this->getResponse()->success(null, $msgOrData, $code);
        }
        return $this->getResponse()->success(null, $data, $code);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function error(string $message = '', int $code = 500, array $data = []): ResponseInterface
    {
        return $this->getResponse()->error($message, $code, $data);
    }

    /**
     * 跳转.
     */
    public function redirect(string $toUrl, int $status = 302, string $schema = 'http'): ResponseInterface
    {
        return $this->getResponse()->redirect($toUrl, $status, $schema);
    }

    /**
     * 下载文件.
     */
    public function _download(string $filePath, string $name = ''): ResponseInterface
    {
        return $this->getResponse()->download($filePath, $name);
    }
}
