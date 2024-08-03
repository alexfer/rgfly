<?php

namespace App\Service\Redis;

interface ConnectionInterface
{
    /**
     * @return \Redis
     */
    public function redis(): \Redis;
}