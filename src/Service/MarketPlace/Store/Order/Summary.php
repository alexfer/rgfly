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
            $products = $order->getStoreOrdersProducts()->toArray();

            $itemSubtotal = $fee = $total = [];

            foreach ($products as $product) {
                $cost = $product->getCost() * $product->getQuantity();
                $discount = $product->getDiscount();
                $fee[$order->getId()][] = $product->getProduct()->getFee();
                $itemSubtotal[$order->getId()][] = $product->getCost() * $product->getQuantity();
                $total[$order->getId()][] = $cost - (($cost * $discount) - $discount) / 100;
            }

            if ($formatted) {
                $summary[] = [
                    'store' => $order->getStore()->getId(),
                    'currency' => Currency::currency($order->getStore()->getCurrency())['symbol'],
                    'fee' => number_format(array_sum($fee[$order->getId()]), 2, '.', ' '),
                    'total' => number_format(round(array_sum($fee[$order->getId()]) + array_sum($total[$order->getId()])), 2, '.', ' '),
                    'itemSubtotal' => number_format(round(array_sum($itemSubtotal[$order->getId()])), 2, '.', ' '),
                ];
            } else {
                $summary[] = [
                    'number' => $order->getNumber(),
                    'store_id' => $order->getStore()->getId(),
                    'store_name' => $order->getStore()->getName(),
                    'currency' => Currency::currency($order->getStore()->getCurrency())['symbol'],
                    'fee' => array_sum($fee[$order->getId()]),
                    'itemSubtotal' => array_sum($itemSubtotal[$order->getId()]),
                    'total' => array_sum($total[$order->getId()]),
                ];
            }
        }
        return $summary;
    }
}