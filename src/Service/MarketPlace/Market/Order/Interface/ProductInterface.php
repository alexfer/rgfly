<?php

namespace App\Service\MarketPlace\Market\Order\Interface;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketOrders;

interface ProductInterface
{
    /**
     * @param MarketCustomer|null $customer
     * @return void
     */
    public function process(?MarketCustomer $customer): void;

    /**
     * @return MarketOrders|null
     */
    public function getOrder(): ?MarketOrders;

    /**
     * @return Market
     */
    public function getMarket(): Market;
}