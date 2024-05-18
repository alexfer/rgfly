<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketCouponCodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketCouponCodeRepository::class)]
class MarketCouponCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketCouponCodes')]
    private ?MarketCoupon $coupon = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column]
    private ?bool $has_used;

    /**
     * @var Collection<int, MarketCouponUsage>
     */
    #[ORM\OneToMany(mappedBy: 'coupon_code', targetEntity: MarketCouponUsage::class)]
    private Collection $marketCouponUsages;

    public function __construct()
    {
        $this->marketCouponUsages = new ArrayCollection();
        $this->has_used = false;
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
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, MarketCouponUsage>
     */
    public function getMarketCouponUsages(): Collection
    {
        return $this->marketCouponUsages;
    }

    public function addMarketCouponUsage(MarketCouponUsage $marketCouponUsage): static
    {
        if (!$this->marketCouponUsages->contains($marketCouponUsage)) {
            $this->marketCouponUsages->add($marketCouponUsage);
            $marketCouponUsage->setCouponCode($this);
        }

        return $this;
    }

    public function removeMarketCouponUsage(MarketCouponUsage $marketCouponUsage): static
    {
        if ($this->marketCouponUsages->removeElement($marketCouponUsage)) {
            // set the owning side to null (unless already changed)
            if ($marketCouponUsage->getCouponCode() === $this) {
                $marketCouponUsage->setCouponCode(null);
            }
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function hasUsed(): ?bool
    {
        return $this->has_used;
    }

    /**
     * @param bool $has_used
     * @return $this
     */
    public function setHasUsed(bool $has_used): static
    {
        $this->has_used = $has_used;

        return $this;
    }
}
