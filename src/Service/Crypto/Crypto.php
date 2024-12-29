<?php declare(strict_types=1);

namespace Essence\Service\Crypto;

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
        $length = openssl_cipher_iv_length(self::$ciphering);
        $iv = openssl_random_pseudo_bytes($length);

        $cipherText = openssl_encrypt($data, self::$ciphering, self::$encryptionKey, OPENSSL_RAW_DATA, $iv);
        $ciphertextHex = bin2hex($cipherText);
        $ivHex = bin2hex($iv);
        return "$ivHex:$ciphertextHex";
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

        if (empty($encryptionKey)) {
            throw new \RuntimeException('Encryption key not found');
        }

        return $encryptionKey;
    }
}