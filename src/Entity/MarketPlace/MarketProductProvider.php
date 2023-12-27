<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductProviderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductProviderRepository::class)]
class MarketProductProvider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'marketProductProvider', cascade: ['persist', 'remove'])]
    private ?MarketProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductProviders')]
    private ?MarketProvider $provider = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?MarketProduct
    {
        return $this->product;
    }

    public function setProduct(?MarketProduct $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getProvider(): ?MarketProvider
    {
        return $this->provider;
    }

    public function setProvider(?MarketProvider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }
}
