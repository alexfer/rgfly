<?php

namespace App\Service\MarketPlace\Store\Coupon\Interface;

use App\Entity\MarketPlace\{Store, StoreCouponCode, StoreCustomer, StoreOrders};
use Symfony\Component\Security\Core\User\UserInterface;

interface ProcessorInterface
{
    /**
     * @param Store $store
     * @param string $type
     * @return array|int
     */
    public function process(Store $store, string $type): array|int;

    /**
     * @param int $relation
     * @param UserInterface|null $user
     * @return bool
     */
    public function getCouponUsage(int $relation, ?UserInterface $user): bool;

    /**
     * @param int $relation
     * @param StoreCustomer|null $customer
     * @return array|null
     */
    public function getCouponUsages(int $relation, ?StoreCustomer $customer): ?array;

    /**
     * @param StoreOrders $order
     * @return void
     */
    public function updateOrderAmount(StoreOrders $order): void;

    /**
     * @param Store $store
     * @return string
     */
    public function discount(Store $store): string;

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