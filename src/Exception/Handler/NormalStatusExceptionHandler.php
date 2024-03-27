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

namespace Mine\Exception\Handler;

use Hyperf\Codec\Json;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Mine\Exception\NormalStatusException;
use Mine\Log\RequestIdHolder;
use Mine\MineRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Class NormalStatusExceptionHandler.
 */
class NormalStatusExceptionHandler extends ExceptionHandler
{
    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stopPropagation();
        $format = [
            'requestId' => RequestIdHolder::getId(),
            'path' => container()->get(MineRequest::class)->getUri()->getPath(),
            'success' => false,
            'message' => $throwable->getMessage(),
        ];
        if ($throwable->getCode() != 200 && $throwable->getCode() != 0) {
            $format['code'] = $throwable->getCode();
        }
        // 这里日志 还是需要打开吧，
        logger('Exception log')->debug($throwable->getMessage());
        return $response->withHeader('Server', 'MineAdmin')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Headers', 'accept-language,authorization,lang,uid,token,Keep-Alive,User-Agent,Cache-Control,Content-Type')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream(Json::encode($format)));
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof NormalStatusException;
    }
}
