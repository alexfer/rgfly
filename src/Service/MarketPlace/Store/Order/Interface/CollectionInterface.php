<?php declare(strict_types=1);

namespace Essence\Service\MarketPlace\Store\Order\Interface;

use Essence\Entity\MarketPlace\StoreCustomer;

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