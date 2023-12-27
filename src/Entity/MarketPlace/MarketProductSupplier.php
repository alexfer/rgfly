<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductSupplierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductSupplierRepository::class)]
class MarketProductSupplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'marketProductSupplier', cascade: ['persist', 'remove'])]
    private ?MarketProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductSuppliers')]
    private ?MarketSupplier $supplier = null;

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

    public function getSupplier(): ?MarketSupplier
    {
        return $this->supplier;
    }

    public function setSupplier(?MarketSupplier $supplier): static
    {
        $this->supplier = $supplier;

        return $this;
    }
}
