<?php

namespace Inno\Service\MarketPlace\Store\Order\Interface;

use Inno\Entity\MarketPlace\Store;
use Inno\Entity\MarketPlace\StoreCustomer;
use Inno\Entity\MarketPlace\StoreOrders;

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