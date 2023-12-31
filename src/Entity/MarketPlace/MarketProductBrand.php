<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductBrandRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductBrandRepository::class)]
class MarketProductBrand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'marketProductBrand', cascade: ['persist', 'remove'])]
    private ?MarketProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductBrands')]
    private ?MarketBrand $brand = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?MarketProduct
    {
        return $this->product;
    }

    public function setProduct(?MarketProduct $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getBrand(): ?MarketBrand
    {
        return $this->brand;
    }

    public function setBrand(?MarketBrand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }
}
