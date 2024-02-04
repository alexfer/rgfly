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

    #[ORM\Column]
    private ?int $in_use = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->in_use = 1;
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

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     * @return $this
     */
    public function setExtra(array $extra): static
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInUse(): ?int
    {
        return $this->in_use;
    }

    /**
     * @param int $in_use
     * @return $this
     */
    public function setInUse(int $in_use): static
    {
        $this->in_use = $in_use;

        return $this;
    }
}
