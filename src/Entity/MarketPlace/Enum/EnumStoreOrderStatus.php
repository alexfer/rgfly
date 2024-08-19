<?php

namespace App\Entity\MarketPlace\Enum;

enum EnumStoreOrderStatus: string
{
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Returned = 'returned';
    case Processing = 'processing';
    case Confirmed = 'confirmed';
}
