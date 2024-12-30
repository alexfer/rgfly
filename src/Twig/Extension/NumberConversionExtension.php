<?php

namespace Inno\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NumberConversionExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_number', [$this, 'shortNumber']),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'format_number';
    }

    /**
     * @param int $num
     * @return string
     */
    public static function shortNumber(int $num): string
    {
        $units = ['', 'K', 'M', 'B', 'T'];
        for ($i = 0; $num >= 1000; $i++) {
            $num /= 1000;
        }
        return round($num, 1) . $units[$i];
    }
}