<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketProductManufacturerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductManufacturerRepository::class)]
class MarketProductManufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'marketProductManufacturer', cascade: ['persist', 'remove'])]
    private ?MarketProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductManufacturers')]
    private ?MarketManufacturer $manufacturer = null;

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
     * @return MarketManufacturer|null
     */
    public function getManufacturer(): ?MarketManufacturer
    {
        return $this->manufacturer;
    }

    /**
     * @param MarketManufacturer|null $manufacturer
     * @return $this
     */
    public function setManufacturer(?MarketManufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }
}
