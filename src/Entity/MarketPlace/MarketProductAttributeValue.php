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

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return MarketProductAttribute|null
     */
    public function getAttribute(): ?MarketProductAttribute
    {
        return $this->attribute;
    }

    /**
     * @param MarketProductAttribute|null $attribute
     * @return $this
     */
    public function setAttribute(?MarketProductAttribute $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }
}
