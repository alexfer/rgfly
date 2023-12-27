<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketManufacturerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketManufacturerRepository::class)]
class MarketManufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketManufacturers')]
    private ?Market $market = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'manufacturer', targetEntity: MarketProductManufacturer::class)]
    private Collection $marketProductManufacturers;

    public function __construct()
    {
        $this->marketProductManufacturers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarket(): ?Market
    {
        return $this->market;
    }

    public function setMarket(?Market $market): static
    {
        $this->market = $market;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, MarketProductManufacturer>
     */
    public function getMarketProductManufacturers(): Collection
    {
        return $this->marketProductManufacturers;
    }

    public function addMarketProductManufacturer(MarketProductManufacturer $marketProductManufacturer): static
    {
        if (!$this->marketProductManufacturers->contains($marketProductManufacturer)) {
            $this->marketProductManufacturers->add($marketProductManufacturer);
            $marketProductManufacturer->setManufacturer($this);
        }

        return $this;
    }

    public function removeMarketProductManufacturer(MarketProductManufacturer $marketProductManufacturer): static
    {
        if ($this->marketProductManufacturers->removeElement($marketProductManufacturer)) {
            // set the owning side to null (unless already changed)
            if ($marketProductManufacturer->getManufacturer() === $this) {
                $marketProductManufacturer->setManufacturer(null);
            }
        }

        return $this;
    }
}
