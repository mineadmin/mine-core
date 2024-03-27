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

namespace Mine;

use Hyperf\HttpServer\Server;

class MineServer extends Server
{
    protected ?string $serverName = 'MineAdmin';

    protected $routes;

    public function onRequest($request, $response): void
    {
        parent::onRequest($request, $response);
        $this->bootstrap();
    }

    /**
     * MineServer bootstrap.
     */
    protected function bootstrap(): void {}
}
