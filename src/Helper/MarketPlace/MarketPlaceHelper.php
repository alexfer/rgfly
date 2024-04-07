<?php

namespace App\Helper\MarketPlace;

class MarketPlaceHelper
{

    /**
     * @param int $id
     * @param int $length
     * @param string|null $key
     * @return string
     */
    public static function slug(int $id, int $length = 10, string $key = null): string
    {
        $parts = [
            $key ?: 'p',
            $id,
            substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $length),
        ];

        return join('-', $parts);
    }

    /**
     * @param int $length
     * @return string
     */
    public static function orderNumber(int $length): string {
        $today = date("Ymd");
        $rand = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $length);
        return $today . strtoupper($rand);
    }
}