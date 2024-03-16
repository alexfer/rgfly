<?php

namespace App\Service\MarketPlace\Market\Order\Interface;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketOrders;

interface MarketOrderProcessorInterface
{
    /**
     * @param string|null $sessionId
     * @param int|null $id
     * @return MarketOrders|null
     */
    public function findOrder(?string $sessionId, int $id = null): ?MarketOrders;

    /**
     * @param MarketOrders|null $order
     * @param MarketCustomer|null $customer
     * @return MarketOrders
     */
    public function processOrder(?MarketOrders $order, ?MarketCustomer $customer): MarketOrders;

    /**
     * @param string|null $sessionId
     * @param array $input
     * @return void
     */
    public function updateQuantity(?string $sessionId, array $input): void;
}