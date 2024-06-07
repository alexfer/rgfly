<?php

namespace App\Service\MarketPlace\Store\Coupon;

use App\Entity\MarketPlace\{Store, StoreCoupon, StoreCouponCode, StoreCouponUsage, StoreCustomer, StoreOrders};
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\Store\Coupon\Interface\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class Processor implements ProcessorInterface
{
    /**
     * @var array|int
     */
    private array|int $coupon;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {

    }

    /**
     * @param StoreOrders $order
     * @return void
     */
    public function updateOrderAmount(StoreOrders $order): void
    {
        $amount = $order->getTotal();

        if ($this->coupon['discount']) {
            $total = $amount * $this->coupon['discount'] / 100;
            $amount = $amount - $total;
        }
        if ($this->coupon['price']) {
            $amount = $amount - $this->coupon['price'];
        }

        $order->setTotal($amount);
        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * @return StoreCoupon
     */
    private function Coupon(): StoreCoupon
    {
        return $this->em->getRepository(StoreCoupon::class)->find($this->coupon['id']);
    }

    /**
     * @param Store $store
     * @param string $type
     * @return array|int
     */
    public function process(Store $store, string $type = StoreCoupon::COUPON_ORDER): array|int
    {
        $coupon = $this->em->getRepository(StoreCoupon::class)
            ->getSingleActive($store, $type);
        $this->coupon = !$coupon ? $coupon : $coupon['coupon'];
        return $this->coupon;
    }

    /**
     * @param int $relation
     * @param UserInterface|null $user
     * @return bool
     */
    public function getCouponUsage(int $relation, ?UserInterface $user): bool
    {
        return (bool)$this->em->getRepository(StoreCouponUsage::class)
            ->findOneBy([
                'coupon' => $this->Coupon(),
                'relation' => $relation,
                'customer' => $user,
            ]);
    }

    /**
     * @param int $relation
     * @param StoreCustomer|null $customer
     * @return array|null
     */
    public function getCouponUsages(int $relation, ?StoreCustomer $customer): ?array
    {
        return $this->em->getRepository(StoreCouponUsage::class)
            ->getCouponUsages($relation, $customer);
    }

    /**
     * @param Store $store
     * @return string
     */
    public function discount(Store $store): string
    {
        $currency = Currency::currency($store->getCurrency());
        $discount = $this->coupon['price'] ? number_format($this->coupon['price'], 2, ',', ' ') . $currency['symbol'] : null;

        if ($this->coupon['discount']) {
            $discount = sprintf("%d%%", $this->coupon['discount']);
        }
        return $discount;
    }

    /**
     * @param UserInterface $user
     * @param int $orderId
     * @param StoreCouponCode $code
     * @return void
     */
    public function setInuse(
        UserInterface   $user,
        int             $orderId,
        StoreCouponCode $code,
    ): void
    {
        $customer = $this->em->getRepository(StoreCustomer::class)->findOneBy(['member' => $user]);

        $couponUsage = new StoreCouponUsage();
        $couponUsage->setCustomer($customer)
            ->setCoupon($this->Coupon())
            ->setRelation($orderId)
            ->setCouponCode($code);

        $this->em->persist($couponUsage);
        $this->em->flush();
    }

    /**
     * @param string $code
     * @return StoreCouponCode|null
     */
    public function validate(string $code): ?StoreCouponCode
    {
        return $this->em->getRepository(StoreCouponCode::class)->findOneBy([
            'coupon' => $this->Coupon(),
            'code' => strtoupper($code),
        ]);
    }
}