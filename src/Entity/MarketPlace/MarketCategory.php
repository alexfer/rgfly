<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketCategoryRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MarketCategoryRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'slug.unique')]
class MarketCategory
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 512)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 512, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?int $level = null;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: MarketCategory::class)]
    private Collection $children;


    /**
     * @var MarketCategory|null
     */
    #[ORM\ManyToOne(targetEntity: MarketCategory::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?MarketCategory $parent = null;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column]
    private ?DateTimeImmutable $created_at;

    /**
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deleted_at = null;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: MarketCategoryProduct::class)]
    private Collection $marketCategoryProducts;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->marketCategoryProducts = new ArrayCollection();
        $this->children = new ArrayCollection();
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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     *
     * @param string|null $description
     * @return static
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

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
     * @return int|null
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     *
     * @param int $level
     * @return static
     */
    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, MarketCategory>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param MarketCategory $children
     * @return $this
     */
    public function addChildren(MarketCategory $children): static
    {
        $this->children[] = $children;
        return $this;
    }

    /**
     * @return MarketCategory|null
     */
    public function getParent(): ?MarketCategory
    {
        return $this->parent;
    }

    /**
     * @param MarketCategory|null $parent
     * @return $this
     */
    public function setParent(?MarketCategory $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     *
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     *
     * @param DateTimeImmutable $created_at
     * @return static
     */
    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

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
     *
     * @param MarketCategoryProduct $marketCategoryProduct
     * @return static
     */
    public function addMarketCategoryProduct(MarketCategoryProduct $marketCategoryProduct): static
    {
        if (!$this->marketCategoryProducts->contains($marketCategoryProduct)) {
            $this->marketCategoryProducts->add($marketCategoryProduct);
            $marketCategoryProduct->setCategory($this);
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
            if ($marketCategoryProduct->getCategory() === $this) {
                $marketCategoryProduct->setCategory(null);
            }
        }

        return $this;
    }
}
