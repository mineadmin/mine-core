<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Mine\Log\Processor;

use Mine\Log\RequestIdHolder;
use Hyperf\Coroutine\Coroutine;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class SnowflakeRequestIdProcessor implements ProcessorInterface
{
    public function __invoke(array|LogRecord $record)
    {
        RequestIdHolder::setType('snowflake');
        $record['extra']['request_id'] = RequestIdHolder::getId();
        $record['extra']['coroutine_id'] = Coroutine::id();
        return $record;
    }
}
