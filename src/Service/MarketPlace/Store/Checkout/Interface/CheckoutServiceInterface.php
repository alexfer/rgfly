<?php

namespace App\Service\MarketPlace\Store\Checkout\Interface;

use App\Entity\MarketPlace\{StoreCustomer, StoreInvoice, StoreOrders};

interface CheckoutServiceInterface
{
    /**
     * @param string|null $status
     * @param StoreCustomer|null $customer
     * @return StoreOrders|null
     */
    public function findOrder(?string $status, ?StoreCustomer $customer = null): ?StoreOrders;

    /**
     * @param string|null $status
     * @param StoreCustomer|null $customer
     * @return void
     */
    public function updateOrder(?string $status, ?StoreCustomer $customer = null): void;

    /**
     * @param StoreInvoice $invoice
     * @param float $tax
     * @return void
     */
    public function addInvoice(StoreInvoice $invoice, float $tax = 0): void;

    /**
     * @return int
     */
    public function countOrders(): int;

    /**
     * @return array
     */
    public function sum(): array;

}