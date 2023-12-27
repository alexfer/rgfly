<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductManufacturerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductManufacturerRepository::class)]
class MarketProductManufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'marketProductManufacturer', cascade: ['persist', 'remove'])]
    private ?MarketProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductManufacturers')]
    private ?MarketManufacturer $manufacturer = null;

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

    public function getManufacturer(): ?MarketManufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?MarketManufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }
}
