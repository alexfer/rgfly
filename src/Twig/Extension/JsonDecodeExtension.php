<?php

namespace Inno\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JsonDecodeExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('jsonDecode', [$this, 'jsonDecode']),
        ];
    }

    /**
     * @param string $json
     * @return array
     */
    public function jsonDecode(string $json): array
    {
        return json_decode($json, true);
    }
}