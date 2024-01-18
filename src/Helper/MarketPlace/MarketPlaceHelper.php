<?php

namespace App\Helper\MarketPlace;

class MarketPlaceHelper
{
    /**
     * @param int $id
     * @param int $length
     * @return string
     */
    public static function slug(int $id, int $length = 10): string
    {
        $parts = [
            'p',
            $id,
            substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $length),
        ];

        return join('-', $parts);
    }
}