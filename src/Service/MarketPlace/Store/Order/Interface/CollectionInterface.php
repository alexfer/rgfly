<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

use App\Entity\MarketPlace\StoreCustomer;

interface CollectionInterface
{
    /**
     * @param array|null $payload
     * @return array|null
     */
    public function collection(array $payload = null): ?array;

    /**
     * @param StoreCustomer|null $customer
     * @return array|null
     */
    public function getOrders(?StoreCustomer $customer = null): ?array;

    /**
     * @return array|null
     */
    public function getOrderProducts(): ?array;
}