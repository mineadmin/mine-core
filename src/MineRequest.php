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

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Request;

class MineRequest extends Request
{
    /**
     * MineResponse.
     */
    #[Inject]
    protected MineResponse $response;

    /**
     * 获取请求IP.
     */
    public function ip(): string
    {
        $ip = $this->getServerParams()['remote_addr'] ?? '0.0.0.0';
        $headers = $this->getHeaders();

        if (isset($headers['x-real-ip'])) {
            $ip = $headers['x-real-ip'][0];
        } elseif (isset($headers['x-forwarded-for'])) {
            $ip = $headers['x-forwarded-for'][0];
        } elseif (isset($headers['http_x_forwarded_for'])) {
            $ip = $headers['http_x_forwarded_for'][0];
        }

        return $ip;
    }

    /**
     * 获取协议架构.
     */
    public function getScheme(): string
    {
        if (isset($this->getHeader('X-scheme')[0])) {
            return $this->getHeader('X-scheme')[0] . '://';
        }
        return 'http://';
    }
}
