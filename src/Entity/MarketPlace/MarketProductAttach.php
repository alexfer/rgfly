<?php

namespace App\Entity\MarketPlace;

use App\Entity\Attach;
use App\Repository\MarketPlace\MarketProductAttachRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketProductAttachRepository::class)]
class MarketProductAttach
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductAttaches')]
    private ?MarketProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'marketProductAttaches')]
    private ?Attach $attach = null;

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
     * @return MarketProduct|null
     */
    public function getProduct(): ?MarketProduct
    {
        return $this->product;
    }

    /**
     *
     * @param MarketProduct|null $product
     * @return static
     */
    public function setProduct(?MarketProduct $product): static
    {
        $this->product = $product;

        return $this;
    }

    /**
     *
     * @return Attach|null
     */
    public function getAttach(): ?Attach
    {
        return $this->attach;
    }

    /**
     *
     * @param Attach|null $attach
     * @return static
     */
    public function setAttach(?Attach $attach): static
    {
        $this->attach = $attach;

        return $this;
    }
}
