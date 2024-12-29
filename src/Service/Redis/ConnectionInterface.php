<?php declare(strict_types=1);

namespace Essence\Service\Redis;

interface ConnectionInterface
{
    /**
     * @return \Redis
     */
    public function redis(): \Redis;
}