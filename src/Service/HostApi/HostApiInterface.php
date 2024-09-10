<?php declare(strict_types=1);

namespace App\Service\HostApi;

interface HostApiInterface
{
    /**
     * @param string $ipAddress
     * @return array|null
     */
    public function determine(string $ipAddress): ?array;
}