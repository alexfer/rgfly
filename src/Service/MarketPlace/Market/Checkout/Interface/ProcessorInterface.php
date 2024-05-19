<?php

namespace App\Service\MarketPlace\Market\Checkout\Interface;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
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
     * @param array $coupon
     * @param MarketOrders $order
     * @return void
     */
    public function updateOrderAmount(array $coupon, MarketOrders $order): void;

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

    /**
     * @param Market $market
     * @return array|int
     */
    public function getCoupon(Market $market): array|int;

    /**
     * @param int $couponId
     * @param int $orderId
     * @param MarketCustomer $customer
     * @return bool
     */
    public function getCouponUsage(int $couponId, int $orderId, MarketCustomer $customer): bool;
}