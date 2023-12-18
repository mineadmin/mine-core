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
namespace Mine\Helper;

use Hyperf\HttpServer\Contract\RequestInterface;
use Xmo\JWTAuth\JWT;
use Psr\SimpleCache\InvalidArgumentException;

class AppVerify
{
    /**
     * @var JWT
     */
    protected JWT $jwt;

    public RequestInterface $request;

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
     * 验证token
     * @param String|null $token
     * @param string $scene
     * @return bool
     * @throws InvalidArgumentException
     */
    public function check(?String $token = null, string $scene = 'api'): bool
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
     * @return Jwt
     */
    public function getJwt(): Jwt
    {
        return $this->jwt;
    }

    /**
     * 获取当前APP的信息
     * @return array
     */
    public function getAppInfo(): array
    {
        $params = $this->request->getQueryParams() ?? null;
        return $this->jwt->getParserData($params['access_token']);
    }

    /**
     * 获取apiID
     * @return string
     */
    public function getApiId(): string
    {
        $accessToken = $this->request->getQueryParams()['access_token'] ?? null;
        return (string) $this->jwt->getParserData($accessToken)['id'];
    }

    /**
     * 获取当前APP_ID
     * @return string
     */
    public function getAppId(): string
    {
        $accessToken = $this->request->getQueryParams()['access_token'] ?? null;
        return (string) $this->jwt->getParserData($accessToken)['app_id'];
    }

    /**
     * 获取Token
     * @param array $apiInfo
     * @return string
     * @throws InvalidArgumentException
     */
    public function getToken(array $apiInfo): string
    {
        return $this->jwt->getToken($apiInfo);
    }

    /**
     * 刷新token
     * @return string
     * @throws InvalidArgumentException
     */
    public function refresh(): string
    {
        $accessToken = $this->request->getQueryParams()['access_token'] ?? null;
        return $this->jwt->refreshToken($accessToken);
    }
}