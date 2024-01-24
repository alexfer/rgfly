<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductVariantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductVariantsRepository::class)]
class MarketProductVariants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductVariants')]
    private ?MarketProductAttribute $attribute = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductVariants')]
    private ?MarketProduct $product = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttribute(): ?MarketProductAttribute
    {
        return $this->attribute;
    }

    public function setAttribute(?MarketProductAttribute $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
