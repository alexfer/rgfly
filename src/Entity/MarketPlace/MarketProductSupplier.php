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

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?MarketProduct $product = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?MarketSupplier $supplier = null;

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
     * @return MarketSupplier|null
     */
    public function getSupplier(): ?MarketSupplier
    {
        return $this->supplier;
    }

    /**
     * @param MarketSupplier|null $supplier
     * @return $this
     */
    public function setSupplier(?MarketSupplier $supplier): static
    {
        $this->supplier = $supplier;

        return $this;
    }
}
