<?php

namespace App\Service\Crypto;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Crypto implements CryptoInterface
{
    /**
     * @var string
     */
    private static string $ciphering = "AES-256-CBC";

    /**
     * @var string|null
     */
    private static ?string $encryptionKey = null;

    /**
     * @param ParameterBagInterface $params
     */
    public function __construct(private readonly ParameterBagInterface $params)
    {
        self::$encryptionKey = $this->getKey();
    }

    /**
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $ivLen = openssl_cipher_iv_length(self::$ciphering);
        $iv = openssl_random_pseudo_bytes($ivLen);

        return openssl_encrypt($data, self::$ciphering, self::$encryptionKey, 0, $iv);
    }

    /**
     * @return string
     */
    private function getKey(): string
    {
        $file = sprintf('%s/.encryptionKey', $this->params->get('kernel.project_dir'));
        if (!file_exists($file)) {
            throw new \RuntimeException('Encryption key not found');
        }
        $encryptionKey = file_get_contents($file);
        return trim($encryptionKey);
    }
}