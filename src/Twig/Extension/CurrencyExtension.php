<?php

namespace App\Twig\Extension;

use App\Service\MarketPlace\Currency;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CurrencyExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('currency', [$this, 'currency']),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'currency';
    }

    /**
     * @param string $code
     * @return string
     */
    public function currency(string $code): string
    {
        return Currency::$currencies[$code]['symbol_native'];
    }
}