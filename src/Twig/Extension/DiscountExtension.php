<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Helper\MarketPlace\MarketPlaceHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DiscountExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('discount', $this->discount(...)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('convert', $this->convert(...)),
        ];
    }

    /**
     * @param string $uint
     * @param string|null $currency
     * @return string
     */
    public function convert(string $uint, ?string $currency = null): string
    {
        return match ($uint) {
            'percentage' => '%',
            'stock' => $currency,
        };
    }

    /**
     * @param mixed $cost
     * @param mixed $discount
     * @param mixed $fee
     * @param int $quantity
     * @param string $unit
     * @return string
     */
    public function discount(mixed $cost, mixed $discount, mixed $fee, int $quantity, string $unit): string
    {
        return MarketPlaceHelper::discount($cost, $discount, $fee, $quantity, $unit);
    }
}
