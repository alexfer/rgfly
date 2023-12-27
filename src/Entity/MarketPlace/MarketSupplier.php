<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketSupplierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketSupplierRepository::class)]
class MarketSupplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketSuppliers')]
    private ?Market $market = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 3)]
    private ?string $country = null;

    #[ORM\OneToMany(mappedBy: 'supplier', targetEntity: MarketProductSupplier::class)]
    private Collection $marketProductSuppliers;

    public function __construct()
    {
        $this->marketProductSuppliers = new ArrayCollection();
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, MarketProductSupplier>
     */
    public function getMarketProductSuppliers(): Collection
    {
        return $this->marketProductSuppliers;
    }

    public function addMarketProductSupplier(MarketProductSupplier $marketProductSupplier): static
    {
        if (!$this->marketProductSuppliers->contains($marketProductSupplier)) {
            $this->marketProductSuppliers->add($marketProductSupplier);
            $marketProductSupplier->setSupplier($this);
        }

        return $this;
    }

    public function removeMarketProductSupplier(MarketProductSupplier $marketProductSupplier): static
    {
        if ($this->marketProductSuppliers->removeElement($marketProductSupplier)) {
            // set the owning side to null (unless already changed)
            if ($marketProductSupplier->getSupplier() === $this) {
                $marketProductSupplier->setSupplier(null);
            }
        }

        return $this;
    }
}