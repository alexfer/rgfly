<?php declare(strict_types=1);

namespace App\Service\Crypto;

interface CryptoInterface
{
    /**
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string;
}