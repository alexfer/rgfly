<?php

namespace App\Service\MarketPlace\Store\Coupon\Interface;

use App\Entity\MarketPlace\{Store, StoreCoupon, StoreCouponCode, StoreCustomer, StoreOrders};
use Symfony\Component\Security\Core\User\UserInterface;

interface ProcessorInterface
{
    /**
     * @param Store $market
     * @return array|int
     */
    public function process(Store $market): array|int;

    /**
     * @param int $orderId
     * @param UserInterface|null $user
     * @return bool
     */
    public function getCouponUsage(int $orderId, ?UserInterface $user): bool;

    /**
     * @param StoreOrders $order
     * @return void
     */
    public function updateOrderAmount(StoreOrders $order): void;

    /**
     * @param Store $market
     * @return string
     */
    public function discount(Store $market): string;

    /**
     * @param UserInterface $user
     * @param int $orderId
     * @param StoreCouponCode $code
     * @return void
     */
    public function setInuse(UserInterface $user, int $orderId, StoreCouponCode $code): void;

    /**
     * @param string $code
     * @return StoreCouponCode|null
     */
    public function validate(string $code): ?StoreCouponCode;
}