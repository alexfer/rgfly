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

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?MarketProduct $product = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?MarketProvider $provider = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return MarketProvider|null
     */
    public function getProvider(): ?MarketProvider
    {
        return $this->provider;
    }

    /**
     * @param MarketProvider|null $provider
     * @return $this
     */
    public function setProvider(?MarketProvider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }
}
