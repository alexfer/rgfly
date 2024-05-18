<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Base64Extension extends AbstractExtension
{

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('base64encode', [$this, 'base64encode']),
            new TwigFilter('base64decode', [$this, 'base64decode']),
        ];
    }

    /**
     * @param string $string
     * @return string
     */
    public function base64encode(string $string): string
    {
        return base64_encode($string);
    }

    /**
     * @param string $string
     * @return string
     */
    public function base64decode(string $string): string
    {
        return base64_decode($string);
    }
}