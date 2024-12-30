<?php declare(strict_types=1);

namespace Inno\Service\Redis;

interface ConnectionInterface
{
    /**
     * @return \Redis
     */
    public function redis(): \Redis;
}