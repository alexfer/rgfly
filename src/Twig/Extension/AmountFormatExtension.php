<?php

namespace Essence\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountFormatExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('amount_format', [$this, 'amount_format']),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'amount_format';
    }

    /**
     * @param mixed $value
     * @param int $precision
     * @return string
     */
    public function amount_format(mixed $value, int $precision = 0): string
    {
        $value = round($value, $precision);
        return number_format($value, 2, '.', ' ');
    }
}