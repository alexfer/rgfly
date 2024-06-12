<?php

namespace App\Service\MarketPlace\Store\Coupon;

use App\Entity\MarketPlace\{Store, StoreCoupon, StoreCouponCode, StoreCouponUsage, StoreCustomer};
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
     * @param string $code
     * @return void
     */
    public function setInuse(
        UserInterface $user,
        int           $orderId,
        string        $code,
    ): void
    {
        $customer = $this->em->getRepository(StoreCustomer::class)->findOneBy(['member' => $user]);
        $code = $this->em->getRepository(StoreCouponCode::class)->findOneBy(['code' => strtoupper($code)]);

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
     * @return bool
     */
    public function validate(string $code): bool
    {
        $exists = $this->em->getRepository(StoreCouponCode::class)->verify($this->Coupon(), strtoupper($code));
        return (bool)$exists;
    }
}