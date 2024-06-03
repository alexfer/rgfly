<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

use App\Entity\MarketPlace\{StoreCustomer, StoreOrders};

interface ProcessorInterface
{
    /**
     * @param string|null $sessionId
     * @param int|null $id
     * @return StoreOrders|null
     */
    public function findOrder(?string $sessionId, int $id = null): ?StoreOrders;

    /**
     * @param StoreOrders|null $order
     * @param StoreCustomer|null $customer
     * @return StoreOrders
     */
    public function processOrder(?StoreOrders $order, ?StoreCustomer $customer): StoreOrders;

    /**
     * @param string|null $sessionId
     * @param array $input
     * @return void
     */
    public function updateQuantity(?string $sessionId, array $input): void;
}