<?php

namespace Essence\Service\MarketPlace\Store\Order\Interface;

use Essence\Entity\MarketPlace\Store;
use Essence\Entity\MarketPlace\StoreCustomer;
use Essence\Entity\MarketPlace\StoreOrders;

interface ProductServiceInterface
{
    /**
     * @param StoreCustomer|null $customer
     * @return void
     */
    public function process(?StoreCustomer $customer): void;

    /**
     * @return StoreOrders|null
     */
    public function getOrder(): ?StoreOrders;

    /**
     * @return Store
     */
    public function getStore(): Store;
}