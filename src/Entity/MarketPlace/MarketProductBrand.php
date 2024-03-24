<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductBrandRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductBrandRepository::class)]
class MarketProductBrand
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'marketProductBrand', cascade: ['persist', 'remove'])]
    private ?MarketProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductBrands')]
    private ?MarketBrand $brand = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return MarketBrand|null
     */
    public function getBrand(): ?MarketBrand
    {
        return $this->brand;
    }

    /**
     * @param MarketBrand|null $brand
     * @return $this
     */
    public function setBrand(?MarketBrand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }
}
