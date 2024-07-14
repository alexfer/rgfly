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
    public static function orderNumber(int $length): string
    {
        $today = date("Ymd");
        $rand = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $length);
        return $today . strtoupper($rand);
    }

    /**
     * @param int $num
     * @return string
     */
    public static function shortNumber(int $num): string
    {
        $units = ['', 'K', 'M', 'B', 'T'];
        for ($i = 0; $num >= 1000; $i++) {
            $num /= 1000;
        }
        return round($num, 1) . $units[$i];
    }

    /**
     * @param int $month
     * @return array
     */
    public static function months(int $month): array
    {
        $months = [];
        foreach (range(12, 1) as $i) {
            if ($i <= $month) {
                $months[] = strtolower(date('F', mktime(0, 0, 0, $i, 1)));

            }
        }
        return $months;
    }
}