<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

use App\Entity\MarketPlace\StoreCustomer;

interface CollectionInterface
{
    /**
     * @return array|null
     */
    public function collection(): ?array;

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