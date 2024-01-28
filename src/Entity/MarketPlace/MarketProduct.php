<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MarketProductRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'slug.unique', errorPath: 'slug')]
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

    #[ORM\Column(length: 512, unique: true, nullable: true)]
    #[Assert\Valid]
    private ?string $slug = null;

    #[ORM\Column]
    private ?int $quantity;

    #[ORM\Column(type: 'float')]
    private ?float $cost;

    #[ORM\Column]
    private ?DateTime $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updated_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deleted_at = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: MarketCategoryProduct::class)]
    private Collection $marketCategoryProducts;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: MarketProductAttach::class)]
    private Collection $marketProductAttaches;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Market $market = null;

    #[ORM\OneToOne(mappedBy: 'product', cascade: ['persist', 'remove'])]
    private ?MarketProductBrand $marketProductBrand = null;

    #[ORM\OneToOne(mappedBy: 'product', cascade: ['persist', 'remove'])]
    private ?MarketProductSupplier $marketProductSupplier = null;

    #[ORM\OneToOne(mappedBy: 'product', cascade: ['persist', 'remove'])]
    private ?MarketProductManufacturer $marketProductManufacturer = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: MarketOrdersProduct::class)]
    private Collection $marketOrdersProducts;

    #[ORM\Column(length: 80)]
    private ?string $short_name = null;

    #[ORM\Column(nullable: true)]
    private ?float $discount = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: MarketProductAttribute::class)]
    private Collection $marketProductAttributes;

    #[ORM\Column(length: 255)]
    private ?string $sku = null;

    public function __construct()
    {
        $this->created_at = new DateTime();
        $this->marketCategoryProducts = new ArrayCollection();
        $this->marketProductAttaches = new ArrayCollection();
        $this->marketOrdersProducts = new ArrayCollection();
        $this->marketProductAttributes = new ArrayCollection();
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
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): static
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
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): static
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

    /**
     * @param int|null $quantity
     * @return $this
     */
    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     *
     * @return float|null
     */
    public function getCost(): ?float
    {
        return $this->cost;
    }

    /**
     * @param float|null $cost
     * @return $this
     */
    public function setCost(?float $cost): static
    {
        $this->cost = round($cost, 2);

        return $this;
    }

    /**
     *
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    /**
     *
     * @param DateTime $created_at
     * @return static
     */
    public function setCreatedAt(DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     *
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     *
     * @param DateTimeInterface|null $updated_at
     * @return static
     */
    public function setUpdatedAt(?DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     *
     * @return DateTimeInterface|null
     */
    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    /**
     *
     * @param DateTimeInterface|null $deleted_at
     * @return static
     */
    public function setDeletedAt(?DateTimeInterface $deleted_at): static
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

    /**
     * @param MarketCategoryProduct $marketCategoryProduct
     * @return $this
     */
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

    /**
     * @param MarketProductAttach $marketProductAttach
     * @return $this
     */
    public function addMarketProductAttach(MarketProductAttach $marketProductAttach): static
    {
        if (!$this->marketProductAttaches->contains($marketProductAttach)) {
            $this->marketProductAttaches->add($marketProductAttach);
            $marketProductAttach->setProduct($this);
        }

        return $this;
    }

    /**
     * @param MarketProductAttach $marketProductAttach
     * @return $this
     */
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

    /**
     * @return Market|null
     */
    public function getMarket(): ?Market
    {
        return $this->market;
    }

    /**
     * @param Market|null $market
     * @return $this
     */
    public function setMarket(?Market $market): static
    {
        $this->market = $market;

        return $this;
    }

    /**
     * @return MarketProductBrand|null
     */
    public function getMarketProductBrand(): ?MarketProductBrand
    {
        return $this->marketProductBrand;
    }

    /**
     * @param MarketProductBrand|null $marketProductBrand
     * @return $this
     */
    public function setMarketProductBrand(?MarketProductBrand $marketProductBrand): static
    {
        // unset the owning side of the relation if necessary
        if ($marketProductBrand === null && $this->marketProductBrand !== null) {
            $this->marketProductBrand->setProduct(null);
        }

        // set the owning side of the relation if necessary
        if ($marketProductBrand !== null && $marketProductBrand->getProduct() !== $this) {
            $marketProductBrand->setProduct($this);
        }

        $this->marketProductBrand = $marketProductBrand;

        return $this;
    }

    /**
     * @return MarketProductSupplier|null
     */
    public function getMarketProductSupplier(): ?MarketProductSupplier
    {
        return $this->marketProductSupplier;
    }

    /**
     * @param MarketProductSupplier|null $marketProductSupplier
     * @return $this
     */
    public function setMarketProductSupplier(?MarketProductSupplier $marketProductSupplier): static
    {
        // unset the owning side of the relation if necessary
        if ($marketProductSupplier === null && $this->marketProductSupplier !== null) {
            $this->marketProductSupplier->setProduct(null);
        }

        // set the owning side of the relation if necessary
        if ($marketProductSupplier !== null && $marketProductSupplier->getProduct() !== $this) {
            $marketProductSupplier->setProduct($this);
        }

        $this->marketProductSupplier = $marketProductSupplier;

        return $this;
    }

    /**
     * @return MarketProductManufacturer|null
     */
    public function getMarketProductManufacturer(): ?MarketProductManufacturer
    {
        return $this->marketProductManufacturer;
    }

    /**
     * @param MarketProductManufacturer|null $marketProductManufacturer
     * @return $this
     */
    public function setMarketProductManufacturer(?MarketProductManufacturer $marketProductManufacturer): static
    {
        // unset the owning side of the relation if necessary
        if ($marketProductManufacturer === null && $this->marketProductManufacturer !== null) {
            $this->marketProductManufacturer->setProduct(null);
        }

        // set the owning side of the relation if necessary
        if ($marketProductManufacturer !== null && $marketProductManufacturer->getProduct() !== $this) {
            $marketProductManufacturer->setProduct($this);
        }

        $this->marketProductManufacturer = $marketProductManufacturer;

        return $this;
    }

    /**
     * @return Collection<int, MarketOrdersProduct>
     */
    public function getMarketOrdersProducts(): Collection
    {
        return $this->marketOrdersProducts;
    }

    /**
     * @param MarketOrdersProduct $marketOrdersProduct
     * @return $this
     */
    public function addMarketOrdersProduct(MarketOrdersProduct $marketOrdersProduct): static
    {
        if (!$this->marketOrdersProducts->contains($marketOrdersProduct)) {
            $this->marketOrdersProducts->add($marketOrdersProduct);
            $marketOrdersProduct->setProduct($this);
        }

        return $this;
    }

    /**
     * @param MarketOrdersProduct $marketOrdersProduct
     * @return $this
     */
    public function removeMarketOrdersProduct(MarketOrdersProduct $marketOrdersProduct): static
    {
        if ($this->marketOrdersProducts->removeElement($marketOrdersProduct)) {
            // set the owning side to null (unless already changed)
            if ($marketOrdersProduct->getProduct() === $this) {
                $marketOrdersProduct->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShortName(): ?string
    {
        return $this->short_name;
    }

    /**
     * @param string|null $short_name
     * @return $this
     */
    public function setShortName(?string $short_name): static
    {
        $this->short_name = $short_name;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    /**
     * @param float|null $discount
     * @return $this
     */
    public function setDiscount(?float $discount): static
    {
        $this->discount = round($discount, 2);

        return $this;
    }

    /**
     * @return Collection<int, MarketProductAttribute>
     */
    public function getMarketProductAttributes(): Collection
    {
        return $this->marketProductAttributes;
    }

    public function addMarketProductAttribute(MarketProductAttribute $marketProductAttribute): static
    {
        if (!$this->marketProductAttributes->contains($marketProductAttribute)) {
            $this->marketProductAttributes->add($marketProductAttribute);
            $marketProductAttribute->setProduct($this);
        }

        return $this;
    }

    public function removeMarketProductAttribute(MarketProductAttribute $marketProductAttribute): static
    {
        if ($this->marketProductAttributes->removeElement($marketProductAttribute)) {
            // set the owning side to null (unless already changed)
            if ($marketProductAttribute->getProduct() === $this) {
                $marketProductAttribute->setProduct(null);
            }
        }

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }
}
