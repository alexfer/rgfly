<?php

namespace App\Service\Crypto;

interface CryptoInterface
{
    /**
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string;
}