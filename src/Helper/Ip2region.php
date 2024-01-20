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

namespace Mine\Helper;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Support\Composer;

class Ip2region
{
    protected \XdbSearcher $searcher;

    /**
     * @see https://github.com/zoujingli/ip2region
     * @throws \Exception
     */
    public function __construct(protected StdoutLoggerInterface $logger)
    {
        $composerLoader = Composer::getLoader();
        $path = $composerLoader->findFile(\XdbSearcher::class);

        $dbFile = dirname(realpath($path)) . '/ip2region.xdb';

        // 1、从 dbPath 加载整个 xdb 到内存。
        $cBuff = \XdbSearcher::loadContentFromFile($dbFile);
        if ($cBuff === null) {
            $this->logger->error('failed to load content buffer from {db_file}', ['db_file' => $dbFile]);
            return;
        }
        // 2、使用全局的 cBuff 创建带完全基于内存的查询对象。
        $this->searcher = \XdbSearcher::newWithBuffer($cBuff);

        // 备注：并发使用，用整个 xdb 缓存创建的 searcher 对象可以安全用于并发。
    }

    public function search(string $ip): string
    {
        $region = $this->searcher->search($ip);

        if (! $region) {
            return t('jwt.unknown');
        }

        [$country, $number, $province, $city, $network] = explode('|', $region);
        if ($country == '中国') {
            return $province . '-' . $city . ':' . $network;
        }
        if ($country == '0') {
            return t('jwt.unknown');
        }
        return $country;
    }
}
