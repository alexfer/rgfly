<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountFormat extends AbstractExtension
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
     * @return string
     */
    public function amount_format(mixed $value): string
    {
        return number_format(round($value, 0, PHP_ROUND_HALF_UP), 2, '.', ' ');
    }
}