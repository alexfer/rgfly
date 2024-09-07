<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

use App\Entity\MarketPlace\{StoreCustomer, StoreOrders};

interface ProcessorInterface
{
    /**
     * @param int|null $id
     * @param string|null $sessionId
     * @return StoreOrders|null
     */
    public function findOrder(int $id = null, ?string $sessionId = null): ?StoreOrders;

    /**
     * @param StoreOrders|null $order
     * @param StoreCustomer|null $customer
     * @return StoreOrders
     */
    public function processOrder(?StoreOrders $order, ?StoreCustomer $customer): StoreOrders;

    /**
     * @param array $input
     * @return void
     */
    public function updateQuantity(array $input): void;

    /**
     * @return string
     */
    public function getSessionId(): string;
}