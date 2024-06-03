<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

interface CollectionInterface
{
    /**
     * @param string|null $sessionId
     * @return array|null
     */
    public function collection(?string $sessionId): ?array;

    /**
     * @param string|null $sessId
     * @return array|null
     */
    public function getOrders(?string $sessId = null): ?array;

    /**
     * @param string|null $sessId
     * @return int|null
     */
    public function getOrderProducts(?string $sessId = null): ?int;
}