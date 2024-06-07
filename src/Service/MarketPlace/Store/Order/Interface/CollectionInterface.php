<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

use App\Entity\MarketPlace\StoreCustomer;

interface CollectionInterface
{
    /**
     * @param string|null $sessionId
     * @return array|null
     */
    public function collection(?string $sessionId): ?array;

    /**
     * @param string|null $sessId
     * @param StoreCustomer|null $customer
     * @return array|null
     */
    public function getOrders(?string $sessId = null, ?StoreCustomer $customer = null): ?array;

    /**
     * @param string|null $sessId
     * @return int|null
     */
    public function getOrderProducts(?string $sessId = null): ?int;
}