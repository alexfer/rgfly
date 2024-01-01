<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketOrdersProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketOrdersProductRepository::class)]
class MarketOrdersProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketOrdersProducts')]
    private ?MarketOrders $orders = null;

    #[ORM\ManyToOne(inversedBy: 'marketOrdersProducts')]
    private ?MarketProduct $product = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return MarketOrders|null
     */
    public function getOrders(): ?MarketOrders
    {
        return $this->orders;
    }

    /**
     * @param MarketOrders|null $orders
     * @return $this
     */
    public function setOrders(?MarketOrders $orders): static
    {
        $this->orders = $orders;

        return $this;
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
}
