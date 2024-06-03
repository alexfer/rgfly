<?php

namespace App\Service\MarketPlace\Store\Checkout\Interface;

use App\Entity\MarketPlace\StoreInvoice;
use App\Entity\MarketPlace\StoreOrders;

interface ProcessorInterface
{
    /**
     * @param string $sessionId
     * @return StoreOrders|null
     */
    public function findOrder(string $sessionId): ?StoreOrders;

    /**
     * @return void
     */
    public function updateProducts(): void;

    /**
     * @return void
     */
    public function updateOrder(): void;

    /**
     * @param StoreInvoice $invoice
     * @return void
     */
    public function addInvoice(StoreInvoice $invoice): void;

    /**
     * @return int
     */
    public function countOrders(): int;

    /**
     * @return array
     */
    public function sum(): array;

}