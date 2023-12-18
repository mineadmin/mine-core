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

namespace Mine\Log;

use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;
use Hyperf\Snowflake\IdGeneratorInterface;
use Ramsey\Uuid\Uuid;

class RequestIdHolder
{
    public const REQUEST_ID = 'log.request.id';

    private static string $type = 'uuid';

    public static function setType(string $type): void
    {
        if ($type === 'snowflake') {
            self::$type = 'snowflake';
        }
    }

    public static function getId(): string
    {
        if (Coroutine::inCoroutine()) { // 在协程内
            // 本协程内获取
            $request_id = Context::get(self::REQUEST_ID);
            if (is_null($request_id)) {
                // 没有去父协程 获取
                $request_id = Context::get(self::REQUEST_ID, null, Coroutine::parentId());
                if (! is_null($request_id)) {
                    // 写入本协程，以便本协程或本协程下的子协程获取
                    Context::set(self::REQUEST_ID, $request_id);
                }
            }
            // 都没有，重新生成
            if (is_null($request_id)) {
                $request_id = self::getUniqueId();
            }
        } else {
            $request_id = self::getUniqueId();
        }
        return $request_id;
    }

    protected static function getUniqueId(): string
    {
        if (self::$type == 'uuid') {
            $uniqueId = Context::set(self::REQUEST_ID, Uuid::uuid4()->toString());
        } else {
            $uniqueId = strval(Context::set(self::REQUEST_ID, ApplicationContext::getContainer()->get(IdGeneratorInterface::class)->generate()));
        }
        return $uniqueId;
    }
}
