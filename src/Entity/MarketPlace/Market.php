<?php

namespace App\Entity\MarketPlace;

use App\Entity\User;
use App\Repository\MarketPlace\MarketRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MarketRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'slug.unique')]
class Market
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'markets')]
    private ?User $owner = null;

    #[ORM\Column(length: 512)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 512, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'market', targetEntity: MarketProduct::class)]
    private Collection $products;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deleted_at = null;

    #[ORM\OneToMany(mappedBy: 'market', targetEntity: MarketProvider::class)]
    private Collection $marketProviders;

    public function __construct()
    {
        $this->created_at = new DateTime();
        $this->products = new ArrayCollection();
        $this->marketProviders = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return true;
    }

    /**
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User|null $owner
     * @return $this
     */
    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

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
     * @param string $name
     * @return $this
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
     * @return Collection<int, MarketProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param MarketProduct $product
     * @return $this
     */
    public function addProduct(MarketProduct $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setMarket($this);
        }

        return $this;
    }

    /**
     * @param MarketProduct $product
     * @return $this
     */
    public function removeProduct(MarketProduct $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getMarket() === $this) {
                $product->setMarket(null);
            }
        }

        return $this;
    }

    /**
     * @return DateTime|null
     */

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    /**
     * @param DateTime $created_at
     * @return $this
     */
    public function setCreatedAt(DateTime $created_at): static
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
     * @return Collection<int, MarketProvider>
     */
    public function getMarketProviders(): Collection
    {
        return $this->marketProviders;
    }

    public function addMarketProvider(MarketProvider $marketProvider): static
    {
        if (!$this->marketProviders->contains($marketProvider)) {
            $this->marketProviders->add($marketProvider);
            $marketProvider->setMarket($this);
        }

        return $this;
    }

    public function removeMarketProvider(MarketProvider $marketProvider): static
    {
        if ($this->marketProviders->removeElement($marketProvider)) {
            // set the owning side to null (unless already changed)
            if ($marketProvider->getMarket() === $this) {
                $marketProvider->setMarket(null);
            }
        }

        return $this;
    }
}
