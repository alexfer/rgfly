<?php

namespace App\Service\MarketPlace\Store\Order;

use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\Store\Order\Interface\SummaryInterface;

final class Summary implements SummaryInterface
{
    public function summary(array $orders, bool $formatted = false): array
    {
        $summary = [];

        foreach ($orders as $order) {
            $products = $order['products'];
            $id = $order['id'];

            $itemSubtotal = $fee = $total = [];

            foreach ($products as $product) {
                $cost = $product['product']['cost'] + $product['product']['fee'];
                $discount = intval($product['product']['discount']);
                $fee[$id][] = $product['product']['fee'];
                $itemSubtotal[$id][] = $cost + $product['product']['fee'];
                $total[$id][] = $product['quantity'] * round($cost - (($cost * $discount) - $discount) / 100);
            }

            $currency = Currency::currency($order['store']['currency'])['symbol'];

            if ($formatted) {
                $summary[] = [
                    'store' => $order['store']['id'],
                    'currency' => $currency,
                    'tax' => $order['store']['tax'],
                    'fee' => number_format(array_sum($fee[$id]), 2, '.', ' '),
                    'total' => number_format(round(array_sum($fee[$id]) + array_sum($total[$id])), 2, '.', ' '),
                    'itemSubtotal' => number_format(round(array_sum($itemSubtotal[$id])), 2, '.', ' '),
                ];
            } else {
                $summary[] = [
                    'number' => $order['number'],
                    'tax' => $order['tax'],
                    'store_id' => $order['store']['id'],
                    'store_name' => $order['store']['name'],
                    'currency' => $currency,
                    'fee' => array_sum($fee[$id]),
                    'itemSubtotal' => array_sum($itemSubtotal[$id]),
                    'total' => array_sum($total[$id]),
                ];
            }
        }

        return $summary;
    }
}