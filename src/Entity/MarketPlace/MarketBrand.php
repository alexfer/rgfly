<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketBrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketBrandRepository::class)]
class MarketBrand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketBrands')]
    private ?Market $market = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $url = null;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: MarketProductBrand::class)]
    private Collection $marketProductBrands;

    public function __construct()
    {
        $this->marketProductBrands = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return $this
     */
    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection<int, MarketProductBrand>
     */
    public function getMarketProductBrands(): Collection
    {
        return $this->marketProductBrands;
    }

    /**
     * @param MarketProductBrand $marketProductBrand
     * @return $this
     */
    public function addMarketProductBrand(MarketProductBrand $marketProductBrand): static
    {
        if (!$this->marketProductBrands->contains($marketProductBrand)) {
            $this->marketProductBrands->add($marketProductBrand);
            $marketProductBrand->setBrand($this);
        }

        return $this;
    }

    /**
     * @param MarketProductBrand $marketProductBrand
     * @return $this
     */
    public function removeMarketProductBrand(MarketProductBrand $marketProductBrand): static
    {
        if ($this->marketProductBrands->removeElement($marketProductBrand)) {
            // set the owning side to null (unless already changed)
            if ($marketProductBrand->getBrand() === $this) {
                $marketProductBrand->setBrand(null);
            }
        }

        return $this;
    }
}
