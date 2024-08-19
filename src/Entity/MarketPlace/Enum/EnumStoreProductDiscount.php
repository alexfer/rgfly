<?php

namespace App\Entity\MarketPlace\Enum;

enum EnumStoreProductDiscount: string
{
    case Percentage = 'percentage';
    case Stock = 'stock';

    public function getStockName(): string
    {
        return self::Stock->name;
    }
}
