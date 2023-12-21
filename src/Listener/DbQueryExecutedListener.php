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

namespace Mine\Listener;

use Hyperf\Collection\Arr;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Mine\Helper\Str;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function Hyperf\Support\env;

#[Listener]
class DbQueryExecutedListener implements ListenerInterface
{
    protected StdoutLoggerInterface $console;

    private LoggerInterface $logger;

    public function __construct(StdoutLoggerInterface $console, ContainerInterface $container)
    {
        $this->logger = $container->get(LoggerFactory::class)->get('sql', 'sql');
        $this->console = $console;
    }

    public function listen(): array
    {
        return [QueryExecuted::class];
    }

    /**
     * @param QueryExecuted $event
     */
    public function process(object $event): void
    {
        if ($event instanceof QueryExecuted) {
            $sql = $event->sql;
            $offset = 0;
            if (! Arr::isAssoc($event->bindings)) {
                foreach ($event->bindings as $value) {
                    $value = is_array($value) ? json_encode($value) : "'{$value}'";
                    $sql = Str::replaceFirst('?', "{$value}", $sql, $offset);
                }
            }
            if (env('CONSOLE_SQL')) {
                $this->console->info(sprintf('SQL[%s ms] %s ', $event->time, $sql));
                $this->logger->info(sprintf('[%s] %s', $event->time, $sql));
            }
        }
    }
}
