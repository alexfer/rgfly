<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreOrders;

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