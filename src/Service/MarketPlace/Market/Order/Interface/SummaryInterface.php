<?php

namespace App\Service\MarketPlace\Market\Order\Interface;

interface SummaryInterface
{
    /**
     * @param array $orders
     * @param bool $formatted
     * @return array
     */
    public function summary(array $orders, bool $formatted = false): array;
}