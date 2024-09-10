<?php declare(strict_types=1);

namespace App\Service\Redis;

interface ConnectionInterface
{
    /**
     * @return \Redis
     */
    public function redis(): \Redis;
}