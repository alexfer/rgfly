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

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(nullable: true)]
    private ?int $discount = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $event = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $started_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expired_at = null;

    /**
     * @var Collection<int, MarketProduct>
     */
    #[ORM\ManyToMany(targetEntity: MarketProduct::class, inversedBy: 'marketCoupons')]
    private Collection $product;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->product = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return $this
     */
    public function setPrice(?float $price): static
    {
        $this->price = $price;

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

    public function getMarket(): ?Market
    {
        return $this->market;
    }

    public function setMarket(?Market $market): static
    {
        $this->market = $market;

        return $this;
    }

    public function getEvent(): ?int
    {
        return $this->event;
    }

    public function setEvent(?int $event): static
    {
        $this->event = $event;

        return $this;
    }
}
