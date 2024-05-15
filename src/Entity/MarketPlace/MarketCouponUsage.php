<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketCouponUsageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketCouponUsageRepository::class)]
class MarketCouponUsage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketCouponUsages')]
    private ?MarketCoupon $coupon = null;

    #[ORM\ManyToOne(inversedBy: 'marketCouponUsages')]
    private ?MarketCustomer $customer = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $used_at = null;

    public function __construct()
    {
        $this->used_at = new \DateTimeImmutable();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return MarketCoupon|null
     */
    public function getCoupon(): ?MarketCoupon
    {
        return $this->coupon;
    }

    /**
     * @param MarketCoupon|null $coupon
     * @return $this
     */
    public function setCoupon(?MarketCoupon $coupon): static
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * @return MarketCustomer|null
     */
    public function getCustomer(): ?MarketCustomer
    {
        return $this->customer;
    }

    /**
     * @param MarketCustomer|null $customer
     * @return $this
     */
    public function setCustomer(?MarketCustomer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUsedAt(): ?\DateTimeImmutable
    {
        return $this->used_at;
    }

    /**
     * @param \DateTimeImmutable $used_at
     * @return $this
     */
    public function setUsedAt(\DateTimeImmutable $used_at): static
    {
        $this->used_at = $used_at;

        return $this;
    }
}
