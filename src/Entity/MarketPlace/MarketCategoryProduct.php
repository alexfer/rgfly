<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\MarketCategoryProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketCategoryProductRepository::class)]
class MarketCategoryProduct
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marketCategoryProducts')]
    private ?MarketProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'marketCategoryProducts')]
    private ?MarketCategory $category = null;

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
     * @return MarketCategory|null
     */
    public function getCategory(): ?MarketCategory
    {
        return $this->category;
    }

    /**
     *
     * @param MarketCategory|null $category
     * @return static
     */
    public function setCategory(?MarketCategory $category): static
    {
        $this->category = $category;

        return $this;
    }
}
