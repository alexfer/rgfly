<?php

declare(strict_types=1);

namespace Inno\Twig\Extension;

use Inno\Service\Crypto\CryptoInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CryptoExtension extends AbstractExtension
{

    /**
     * @param CryptoInterface $crypto
     */
    public function __construct(private readonly CryptoInterface $crypto)
    {

    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('crypto', [$this, 'crypto']),
        ];
    }

    /**
     * @param string $data
     * @return string
     */
    public function crypto(string $data): string
    {
        return $this->crypto->encrypt($data);
    }
}
