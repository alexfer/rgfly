<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ArraySumExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('array_sum', [$this, 'arr_sum']),
        ];
    }

    public function getName(): string
    {
        return 'array_sum';
    }

    public function arr_sum(array $values): int|float
    {
        return array_sum($values);
    }
}