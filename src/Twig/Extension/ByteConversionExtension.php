<?php

namespace Inno\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ByteConversionExtension extends AbstractExtension
{

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_bytes', [$this, 'formatBytes']),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'format_bytes';
    }

    /**
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public function formatBytes($bytes, int $precision = 2): string
    {
        $base = log($bytes, 1024);
        $suffixes = ['Byte', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}
