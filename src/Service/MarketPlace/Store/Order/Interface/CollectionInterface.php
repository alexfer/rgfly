<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

use App\Entity\MarketPlace\StoreCustomer;

interface CollectionInterface
{
    /**
     * @param string|null $sessionId
     * @return array|null
     */
    public function collection(?string $sessionId = null): ?array;

    /**
     * @param StoreCustomer|null $customer
     * @param string|null $sessionId
     * @return array|null
     */
    public function getOrders(?StoreCustomer $customer = null, ?string $sessionId = null): ?array;

    /**
     * @param string|null $sessionId
     * @return array|null
     */
    public function getOrderProducts(?string $sessionId = null): ?array;
}