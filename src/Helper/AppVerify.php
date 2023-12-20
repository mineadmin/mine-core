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

namespace Mine\Helper;

use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Xmo\JWTAuth\JWT;
use function Hyperf\Support\make;

class AppVerify
{
    public RequestInterface $request;

    protected JWT $jwt;

    /**
     * AppVerify constructor.
     * @param string $scene 场景，默认为default
     */
    public function __construct(string $scene = 'api')
    {
        /* @var JWT $this->jwt */
        $this->jwt = make(JWT::class)->setScene($scene);
        $this->request = make(RequestInterface::class);
    }

    /**
     * 验证token.
     * @throws InvalidArgumentException
     */
    public function check(?string $token = null, string $scene = 'api'): bool
    {
        try {
            if ($this->jwt->checkToken($token, $scene, true, true, true)) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }

    /**
     * 获取JWT对象
     */
    public function getJwt(): Jwt
    {
        return $this->jwt;
    }

    /**
     * 获取当前APP的信息.
     */
    public function getAppInfo(): array
    {
        $params = $this->request->getQueryParams() ?? null;
        return $this->jwt->getParserData($params['access_token']);
    }

    /**
     * 获取apiID.
     */
    public function getApiId(): string
    {
        $accessToken = $this->request->getQueryParams()['access_token'] ?? null;
        return (string) $this->jwt->getParserData($accessToken)['id'];
    }

    /**
     * 获取当前APP_ID.
     */
    public function getAppId(): string
    {
        $accessToken = $this->request->getQueryParams()['access_token'] ?? null;
        return (string) $this->jwt->getParserData($accessToken)['app_id'];
    }

    /**
     * 获取Token.
     * @throws InvalidArgumentException
     */
    public function getToken(array $apiInfo): string
    {
        return $this->jwt->getToken($apiInfo);
    }

    /**
     * 刷新token.
     * @throws InvalidArgumentException
     */
    public function refresh(): string
    {
        $accessToken = $this->request->getQueryParams()['access_token'] ?? null;
        return $this->jwt->refreshToken($accessToken);
    }
}
