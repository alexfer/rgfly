<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketWishlistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketWishlistRepository::class)]
class MarketWishlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketWishlists')]
    private ?MarketCustomer $customer = null;

    #[ORM\ManyToOne(inversedBy: 'marketWishlists')]
    private ?MarketProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'marketWishlists')]
    private ?Market $market = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return MarketProduct|null
     */
    public function getProduct(): ?MarketProduct
    {
        return $this->product;
    }

    /**
     * @param MarketProduct|null $product
     * @return $this
     */
    public function setProduct(?MarketProduct $product): static
    {
        $this->product = $product;

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
}
