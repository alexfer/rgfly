<?php declare(strict_types=1);

namespace Inno\Entity\MarketPlace\Enum;

enum EnumStoreProductDiscount: string
{
    case Percentage = 'percentage';
    case Stock = 'stock';
}
