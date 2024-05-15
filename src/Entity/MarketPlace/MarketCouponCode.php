<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketCouponCodeRepository;
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
}
