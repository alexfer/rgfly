<?php

namespace App\Entity\MarketPlace\Enum;

enum EnumOperation: string
{
    case Xml = 'xml';
    case Json = 'json';
    case Csv = 'csv';
}
