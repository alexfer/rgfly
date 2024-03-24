<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductAttributeRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductAttributeRepository::class)]
class MarketProductAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $in_front = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deleted_at = null;

    #[ORM\OneToMany(mappedBy: 'attribute', targetEntity: MarketProductAttributeValue::class)]
    private Collection $marketProductAttributeValues;

    #[ORM\ManyToOne(inversedBy: 'marketProductAttributes')]
    private ?MarketProduct $product = null;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInFront(): ?int
    {
        return $this->in_front;
    }

    /**
     * @param int $in_front
     * @return $this
     */
    public function setInFront(int $in_front): static
    {
        $this->in_front = $in_front;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param DateTimeImmutable $created_at
     * @return $this
     */
    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    /**
     * @param DateTimeInterface|null $deleted_at
     * @return $this
     */
    public function setDeletedAt(?DateTimeInterface $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

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
}
