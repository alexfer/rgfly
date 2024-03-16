<?php

namespace App\Service\MarketPlace\Market\Order\Interface;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketOrders;

interface MarketOrderProcessorInterface
{
    /**
     * @param string|null $sessionId
     * @return MarketOrders|null
     */
    public function findOrder(?string $sessionId): ?MarketOrders;

    /**
     * @param MarketOrders|null $order
     * @param MarketCustomer|null $customer
     * @return MarketOrders
     */
    public function processOrder(?MarketOrders $order, ?MarketCustomer $customer): MarketOrders;
}