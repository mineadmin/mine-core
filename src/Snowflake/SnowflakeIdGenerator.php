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
/**
 *                       __
 *   ____   __  __  ____/ /
 *  /_  /  / / / / / __  /
 *   / /_ / /_/ / / /_/ /
 *  /___/ \__, /  \__,_/
 *       /____/          众熠达.
 */

namespace Mine\Snowflake;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Snowflake\ConfigurationInterface;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Snowflake\Meta;
use Hyperf\Snowflake\MetaGenerator\RedisMilliSecondMetaGenerator;
use Hyperf\Snowflake\MetaGeneratorInterface;

use function Hyperf\Support\make;

class SnowflakeIdGenerator implements IdGeneratorInterface
{
    protected ConfigurationInterface $config;

    protected MetaGeneratorInterface $metaGenerator;

    public function __construct()
    {
        $configuration = new Configuration();
        $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $beginSecond = $config->get('snowflake.begin_second', MetaGeneratorInterface::DEFAULT_BEGIN_SECOND);
        $this->metaGenerator = make(RedisMilliSecondMetaGenerator::class, [
            $configuration,
            $beginSecond,
            $config,
        ]);

        $this->config = $this->metaGenerator->getConfiguration();
    }

    public function generate(?Meta $meta = null): int
    {
        $meta = $this->meta($meta);

        $interval = $meta->getTimeInterval() << $this->config->getTimestampLeftShift();
        $dataCenterId = $meta->getDataCenterId() << $this->config->getDataCenterIdShift();
        $workerId = $meta->getWorkerId() << $this->config->getWorkerIdShift();

        return $interval | $dataCenterId | $workerId | $meta->getSequence();
    }

    public function degenerate(int $id): Meta
    {
        $interval = $id >> $this->config->getTimestampLeftShift();
        $dataCenterId = $id >> $this->config->getDataCenterIdShift();
        $workerId = $id >> $this->config->getWorkerIdShift();

        return new Meta(
            $interval << $this->config->getDataCenterIdBits() ^ $dataCenterId,
            $dataCenterId << $this->config->getWorkerIdBits() ^ $workerId,
            $workerId << $this->config->getSequenceBits() ^ $id,
            $interval + $this->metaGenerator->getBeginTimestamp(),
            $this->metaGenerator->getBeginTimestamp()
        );
    }

    public function getMetaGenerator(): MetaGeneratorInterface
    {
        return $this->metaGenerator;
    }

    protected function meta(?Meta $meta = null): Meta
    {
        if (is_null($meta)) {
            return $this->metaGenerator->generate();
        }

        return $meta;
    }
}
