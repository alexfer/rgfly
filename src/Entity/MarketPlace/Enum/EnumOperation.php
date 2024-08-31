<?php declare(strict_types=1);

namespace App\Entity\MarketPlace\Enum;

enum EnumOperation: string
{
    case Xlsx = 'xlsx';
    case Xml = 'xml';
    case Json = 'json';

}
