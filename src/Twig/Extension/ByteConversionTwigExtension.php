<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ByteConversionTwigExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('format_bytes', [$this, 'formatBytes']),
        ];
    }

    public function getName()
    {
        return 'format_bytes';
    }

    function formatBytes($bytes, $precision = 2): string
    {
        $base = log($bytes, 1024);
        $suffixes = ['Byte', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}
