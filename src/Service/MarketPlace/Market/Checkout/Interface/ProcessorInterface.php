<?php

namespace App\Service\MarketPlace\Market\Checkout\Interface;

use App\Entity\MarketPlace\MarketInvoice;
use App\Entity\MarketPlace\MarketOrders;

interface ProcessorInterface
{
    /**
     * @param string $sessionId
     * @return MarketOrders|null
     */
    public function findOrder(string $sessionId): ?MarketOrders;

    /**
     * @return void
     */
    public function updateProducts(): void;

    /**
     * @return void
     */
    public function updateOrder(): void;

    /**
     * @param MarketInvoice $invoice
     * @return void
     */
    public function addInvoice(MarketInvoice $invoice): void;

    /**
     * @return int
     */
    public function countOrders(): int;

    /**
     * @return array
     */
    public function sum(): array;
}