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
            $id = $order['id'];
            $currency = Currency::currency($order['store']['currency'])['symbol'];
            $store = $order['store']['id'];

            $itemSubtotal = $total = [];

            foreach ($order['products'] as $product) {
                $cost = $product['product']['cost'] + $product['product']['fee'];
                $discount = intval($product['product']['discount']);
                $itemSubtotal[$id][] = $cost + $product['product']['fee'];
                $total[$id][] = $product['quantity'] * round($cost - (($cost * $discount) - $discount) / 100);
            }

            if ($formatted) {
                $summary[] = [
                    'store' => $store,
                    'currency' => $currency,
                    'tax' => $order['store']['tax'],
                    'total' => number_format(round(array_sum($total[$id])), 2, '.', ' '),
                    'itemSubtotal' => number_format(round(array_sum($itemSubtotal[$id])), 2, '.', ' '),
                ];
            } else {
                $summary[] = [
                    'number' => $order['number'],
                    'tax' => $order['store']['tax'],
                    'store_id' => $store,
                    'store_name' => $order['store']['name'],
                    'currency' => $currency,
                    'cc' => $order['store']['cc'],
                    'total' => array_sum($total[$id]),
                    'itemSubtotal' => array_sum($itemSubtotal[$id]),
                ];
            }
        }

        return $summary;
    }
}