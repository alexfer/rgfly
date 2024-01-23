<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductAttributeValueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductAttributeValueRepository::class)]
class MarketProductAttributeValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductAttributeValues')]
    private ?MarketProductAttribute $attribute = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column]
    private array $extra = [];

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    public function setExtra(array $extra): static
    {
        $this->extra = $extra;

        return $this;
    }
}
