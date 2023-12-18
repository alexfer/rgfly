<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'slug.unique')]
class MarketProduct
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 512)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 512)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deleted_at = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: MarketCategoryProduct::class)]
    private Collection $marketCategoryProducts;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: MarketProductAttach::class)]
    private Collection $marketProductAttaches;

    public function __construct()
    {
        $this->marketCategoryProducts = new ArrayCollection();
        $this->marketProductAttaches = new ArrayCollection();
    }

    /**
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     *
     * @param string $slug
     * @return static
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     *
     * @param string $description
     * @return static
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     *
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     *
     * @param float $price
     * @return static
     */
    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     *
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     *
     * @param \DateTimeImmutable $created_at
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     *
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     *
     * @param \DateTimeInterface|null $updated_at
     * @return static
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     *
     * @return \DateTimeInterface|null
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    /**
     *
     * @param \DateTimeInterface|null $deleted_at
     * @return static
     */
    public function setDeletedAt(?\DateTimeInterface $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return Collection<int, MarketCategoryProduct>
     */
    public function getMarketCategoryProducts(): Collection
    {
        return $this->marketCategoryProducts;
    }

    public function addMarketCategoryProduct(MarketCategoryProduct $marketCategoryProduct): static
    {
        if (!$this->marketCategoryProducts->contains($marketCategoryProduct)) {
            $this->marketCategoryProducts->add($marketCategoryProduct);
            $marketCategoryProduct->setProduct($this);
        }

        return $this;
    }

    /**
     *
     * @param MarketCategoryProduct $marketCategoryProduct
     * @return static
     */
    public function removeMarketCategoryProduct(MarketCategoryProduct $marketCategoryProduct): static
    {
        if ($this->marketCategoryProducts->removeElement($marketCategoryProduct)) {
            // set the owning side to null (unless already changed)
            if ($marketCategoryProduct->getProduct() === $this) {
                $marketCategoryProduct->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MarketProductAttach>
     */
    public function getMarketProductAttaches(): Collection
    {
        return $this->marketProductAttaches;
    }

    public function addMarketProductAttach(MarketProductAttach $marketProductAttach): static
    {
        if (!$this->marketProductAttaches->contains($marketProductAttach)) {
            $this->marketProductAttaches->add($marketProductAttach);
            $marketProductAttach->setProduct($this);
        }

        return $this;
    }

    public function removeMarketProductAttach(MarketProductAttach $marketProductAttach): static
    {
        if ($this->marketProductAttaches->removeElement($marketProductAttach)) {
            // set the owning side to null (unless already changed)
            if ($marketProductAttach->getProduct() === $this) {
                $marketProductAttach->setProduct(null);
            }
        }

        return $this;
    }
}
