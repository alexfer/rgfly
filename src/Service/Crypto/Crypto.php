<?php

namespace App\Service\Crypto;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Crypto implements CryptoInterface
{
    /**
     * @var string
     */
    private static string $ciphering = "aes-256-cbc";

    /**
     * @var string|null
     */
    private static ?string $encryptionKey = null;

    private static ?string $encryptionIV = null;

    /**
     * @param ParameterBagInterface $params
     */
    public function __construct(private readonly ParameterBagInterface $params)
    {
        self::$encryptionKey = base64_decode($this->getKey()['encryption_key']);
        self::$encryptionIV = base64_decode($this->getKey()['encryption_iv']);
    }

    /**
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        return openssl_encrypt($data, self::$ciphering, self::$encryptionKey, 0, self::$encryptionIV);
    }

    /**
     * @return array
     */
    private function getKey(): array
    {
        $file = sprintf('%s/.encryptionKey', $this->params->get('kernel.project_dir'));

        if (!file_exists($file)) {
            throw new \RuntimeException('Encryption key not found');
        }
        $encryptionKeys = file_get_contents($file);

        if (empty($encryptionKeys)) {
            throw new \RuntimeException('Encryption key not found');
        }

        $encryptionKeys = explode("::", $encryptionKeys);

        if (!is_array($encryptionKeys)) {
            throw new \RuntimeException('Encryption keys not found');
        }

        return [
            'encryption_key' => $encryptionKeys[0],
            'encryption_iv' => $encryptionKeys[1],
        ];
    }
}