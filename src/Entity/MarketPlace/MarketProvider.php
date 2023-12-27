<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProviderRepository::class)]
class MarketProvider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketProviders')]
    private ?Market $market = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $url = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: MarketProductProvider::class)]
    private Collection $marketProductProviders;

    public function __construct()
    {
        $this->marketProductProviders = new ArrayCollection();
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
     * @return Collection<int, MarketProductProvider>
     */
    public function getMarketProductProviders(): Collection
    {
        return $this->marketProductProviders;
    }

    public function addMarketProductProvider(MarketProductProvider $marketProductProvider): static
    {
        if (!$this->marketProductProviders->contains($marketProductProvider)) {
            $this->marketProductProviders->add($marketProductProvider);
            $marketProductProvider->setProvider($this);
        }

        return $this;
    }

    public function removeMarketProductProvider(MarketProductProvider $marketProductProvider): static
    {
        if ($this->marketProductProviders->removeElement($marketProductProvider)) {
            // set the owning side to null (unless already changed)
            if ($marketProductProvider->getProvider() === $this) {
                $marketProductProvider->setProvider(null);
            }
        }

        return $this;
    }
}
