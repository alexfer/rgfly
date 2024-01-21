<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductAttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductAttributeRepository::class)]
class MarketProductAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductAttributes')]
    private ?MarketProduct $product = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'attribute', targetEntity: MarketProductAttributeValue::class)]
    private Collection $marketProductAttributeValues;

    public function __construct()
    {
        $this->marketProductAttributeValues = new ArrayCollection();
    }

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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, MarketProductAttributeValue>
     */
    public function getMarketProductAttributeValues(): Collection
    {
        return $this->marketProductAttributeValues;
    }

    /**
     * @param MarketProductAttributeValue $marketProductAttributeValue
     * @return $this
     */
    public function addMarketProductAttributeValue(MarketProductAttributeValue $marketProductAttributeValue): static
    {
        if (!$this->marketProductAttributeValues->contains($marketProductAttributeValue)) {
            $this->marketProductAttributeValues->add($marketProductAttributeValue);
            $marketProductAttributeValue->setAttribute($this);
        }

        return $this;
    }

    /**
     * @param MarketProductAttributeValue $marketProductAttributeValue
     * @return $this
     */
    public function removeMarketProductAttributeValue(MarketProductAttributeValue $marketProductAttributeValue): static
    {
        if ($this->marketProductAttributeValues->removeElement($marketProductAttributeValue)) {
            // set the owning side to null (unless already changed)
            if ($marketProductAttributeValue->getAttribute() === $this) {
                $marketProductAttributeValue->setAttribute(null);
            }
        }

        return $this;
    }
}
