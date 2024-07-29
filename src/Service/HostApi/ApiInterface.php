<?php

namespace App\Service\HostApi;

interface ApiInterface
{
    /**
     * @param string $ipAddress
     * @return array|null
     */
    public function determine(string $ipAddress): ?array;
}