<?php

namespace App\Service\MarketPlace\Market\Coupon;

use App\Entity\MarketPlace\{Market, MarketCoupon, MarketCouponCode, MarketCouponUsage, MarketCustomer, MarketOrders};
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\Market\Coupon\Interface\ProcessorInterface;
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
     * @param MarketOrders $order
     * @return void
     */
    public function updateOrderAmount(MarketOrders $order): void
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
     * @return MarketCoupon
     */
    private function Coupon(): MarketCoupon
    {
        return $this->em->getRepository(MarketCoupon::class)->find($this->coupon['id']);
    }

    /**
     * @param Market $market
     * @return array|int
     */
    public function process(Market $market): array|int
    {
        $coupon = $this->em->getRepository(MarketCoupon::class)
            ->getSingleActive($market, MarketCoupon::COUPON_ORDER);
        $this->coupon = !$coupon ? $coupon : $coupon['coupon'];
        return $this->coupon;
    }

    /**
     * @param int $orderId
     * @param UserInterface|null $user
     * @return bool
     */
    public function getCouponUsage(int $orderId, ?UserInterface $user): bool
    {
        return (bool)$this->em->getRepository(MarketCouponUsage::class)
            ->findOneBy([
                'coupon' => $this->Coupon(),
                'relation' => $orderId,
                'customer' => $user,
            ]);
    }

    /**
     * @param Market $market
     * @return string
     */
    public function discount(Market $market): string
    {
        $currency = Currency::currency($market->getCurrency());
        $discount = $this->coupon['price'] ? number_format($this->coupon['price'], 2, ',', ' ') . $currency['symbol'] : null;

        if ($this->coupon['discount']) {
            $discount = sprintf("%d%%", $this->coupon['discount']);
        }
        return $discount;
    }

    /**
     * @param UserInterface $user
     * @param int $orderId
     * @param MarketCouponCode $code
     * @return void
     */
    public function setInuse(
        UserInterface    $user,
        int              $orderId,
        MarketCouponCode $code,
    ): void
    {
        $customer = $this->em->getRepository(MarketCustomer::class)->findOneBy(['member' => $user]);

        $couponUsage = new MarketCouponUsage();
        $couponUsage->setCustomer($customer)
            ->setCoupon($this->Coupon())
            ->setRelation($orderId)
            ->setCouponCode($code);

        $this->em->persist($couponUsage);
        $this->em->flush();
    }

    /**
     * @param string $code
     * @return MarketCouponCode|null
     */
    public function validate(string $code): ?MarketCouponCode
    {
        return $this->em->getRepository(MarketCouponCode::class)->findOneBy([
            'coupon' => $this->Coupon(),
            'code' => strtoupper($code),
        ]);
    }
}