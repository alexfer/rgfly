<?php declare(strict_types=1);

namespace Inno\Service\MarketPlace\Store\Order\Interface;

interface SummaryInterface
{
    /**
     * @param array $orders
     * @param bool $formatted
     * @return array
     */
    public function summary(array $orders, bool $formatted = false): array;
}