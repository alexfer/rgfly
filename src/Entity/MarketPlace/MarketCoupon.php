<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketCouponRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketCouponRepository::class)]
class MarketCoupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketCoupons')]
    private ?Market $market = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $discount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '2', nullable: true)]
    private ?string $price = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $event = null;

    /**
     * @var Collection<int, MarketProduct>
     */
    #[ORM\ManyToMany(targetEntity: MarketProduct::class, inversedBy: 'marketCoupons')]
    private Collection $product;

    /**
     * @var Collection<int, MarketCouponCode>
     */
    #[ORM\OneToMany(mappedBy: 'coupon', targetEntity: MarketCouponCode::class)]
    private Collection $marketCouponCodes;

    /**
     * @var Collection<int, MarketCouponUsage>
     */
    #[ORM\OneToMany(mappedBy: 'coupon', targetEntity: MarketCouponUsage::class)]
    private Collection $marketCouponUsages;

    #[ORM\Column]
    private ?int $available = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $max_usage = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $order_limit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $promotion_text = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $started_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expired_at = null;

    const string COUPON_ORDER = 'order';

    const string COUPON_PRODUCT = 'product';

    const string COUPON_SHIPMENT = 'shipment';

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->product = new ArrayCollection();
        $this->marketCouponCodes = new ArrayCollection();
        $this->marketCouponUsages = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    /**
     * @param int|null $discount
     * @return $this
     */
    public function setDiscount(?int $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string|null $price
     * @return $this
     */
    public function setPrice(?string $price): static
    {
        $this->price = number_format($price, 2, '.', '');;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable $created_at
     * @return $this
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->started_at;
    }

    /**
     * @param \DateTimeImmutable|null $started_at
     * @return $this
     */
    public function setStartedAt(?\DateTimeImmutable $started_at): static
    {
        $this->started_at = $started_at;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expired_at;
    }

    /**
     * @param \DateTimeImmutable|null $expired_at
     * @return $this
     */
    public function setExpiredAt(?\DateTimeImmutable $expired_at): static
    {
        $this->expired_at = $expired_at;

        return $this;
    }

    /**
     * @return Collection<int, MarketProduct>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    /**
     * @param MarketProduct $product
     * @return $this
     */
    public function addProduct(MarketProduct $product): static
    {
        if (!$this->product->contains($product)) {
            $this->product->add($product);
        }

        return $this;
    }

    /**
     * @param MarketProduct $product
     * @return $this
     */
    public function removeProduct(MarketProduct $product): static
    {
        $this->product->removeElement($product);

        return $this;
    }

    /**
     * @return Market|null
     */
    public function getMarket(): ?Market
    {
        return $this->market;
    }

    /**
     * @param Market|null $market
     * @return $this
     */
    public function setMarket(?Market $market): static
    {
        $this->market = $market;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEvent(): ?int
    {
        return $this->event;
    }

    /**
     * @param int|null $event
     * @return $this
     */
    public function setEvent(?int $event): static
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxUsage(): ?int
    {
        return $this->max_usage;
    }

    /**
     * @param int $max_usage
     * @return $this
     */
    public function setMaxUsage(int $max_usage): static
    {
        $this->max_usage = $max_usage;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderLimit(): ?int
    {
        return $this->order_limit;
    }

    /**
     * @param int $order_limit
     * @return $this
     */
    public function setOrderLimit(int $order_limit): static
    {
        $this->order_limit = $order_limit;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPromotionText(): ?string
    {
        return $this->promotion_text;
    }

    /**
     * @param string|null $promotion_text
     * @return $this
     */
    public function setPromotionText(?string $promotion_text): static
    {
        $this->promotion_text = $promotion_text;

        return $this;
    }

    /**
     * @return Collection<int, MarketCouponCode>
     */
    public function getMarketCouponCodes(): Collection
    {
        return $this->marketCouponCodes;
    }

    /**
     * @param MarketCouponCode $marketCouponCode
     * @return $this
     */
    public function addMarketCouponCode(MarketCouponCode $marketCouponCode): static
    {
        if (!$this->marketCouponCodes->contains($marketCouponCode)) {
            $this->marketCouponCodes->add($marketCouponCode);
            $marketCouponCode->setCoupon($this);
        }

        return $this;
    }

    /**
     * @param MarketCouponCode $marketCouponCode
     * @return $this
     */
    public function removeMarketCouponCode(MarketCouponCode $marketCouponCode): static
    {
        if ($this->marketCouponCodes->removeElement($marketCouponCode)) {
            // set the owning side to null (unless already changed)
            if ($marketCouponCode->getCoupon() === $this) {
                $marketCouponCode->setCoupon(null);
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAvailable(): ?int
    {
        return $this->available;
    }

    /**
     * @param int $available
     * @return $this
     */
    public function setAvailable(int $available): static
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return Collection<int, MarketCouponUsage>
     */
    public function getMarketCouponUsages(): Collection
    {
        return $this->marketCouponUsages;
    }

    /**
     * @param MarketCouponUsage $marketCouponUsage
     * @return $this
     */
    public function addMarketCouponUsage(MarketCouponUsage $marketCouponUsage): static
    {
        if (!$this->marketCouponUsages->contains($marketCouponUsage)) {
            $this->marketCouponUsages->add($marketCouponUsage);
            $marketCouponUsage->setCoupon($this);
        }

        return $this;
    }

    /**
     * @param MarketCouponUsage $marketCouponUsage
     * @return $this
     */
    public function removeMarketCouponUsage(MarketCouponUsage $marketCouponUsage): static
    {
        if ($this->marketCouponUsages->removeElement($marketCouponUsage)) {
            // set the owning side to null (unless already changed)
            if ($marketCouponUsage->getCoupon() === $this) {
                $marketCouponUsage->setCoupon(null);
            }
        }

        return $this;
    }

}
