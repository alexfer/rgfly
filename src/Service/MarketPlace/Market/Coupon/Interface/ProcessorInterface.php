<?php

namespace App\Service\MarketPlace\Market\Coupon\Interface;

use App\Entity\MarketPlace\{Market, MarketCoupon, MarketCouponCode, MarketCustomer, MarketOrders};
use Symfony\Component\Security\Core\User\UserInterface;

interface ProcessorInterface
{
    /**
     * @param Market $market
     * @return array|int
     */
    public function process(Market $market): array|int;

    /**
     * @param int $orderId
     * @param UserInterface|null $user
     * @return bool
     */
    public function getCouponUsage(int $orderId, ?UserInterface $user): bool;

    /**
     * @param MarketOrders $order
     * @return void
     */
    public function updateOrderAmount(MarketOrders $order): void;

    /**
     * @param Market $market
     * @return string
     */
    public function discount(Market $market): string;

    /**
     * @param UserInterface $user
     * @param int $orderId
     * @param MarketCouponCode $code
     * @return void
     */
    public function setInuse(UserInterface $user, int $orderId, MarketCouponCode $code): void;

    /**
     * @param string $code
     * @return MarketCouponCode|null
     */
    public function validate(string $code): ?MarketCouponCode;
}