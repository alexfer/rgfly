<?php

namespace App\Entity\MarketPlace;

use App\Repository\MarketPlace\StoreCategoryProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreCategoryProductRepository::class)]
class StoreCategoryProduct
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'storeCategoryProducts')]
    private ?StoreProduct $product = null;

    #[ORM\ManyToOne(inversedBy: 'storeCategoryProducts')]
    private ?StoreCategory $category = null;

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
     * @return StoreProduct|null
     */
    public function getProduct(): ?StoreProduct
    {
        return $this->product;
    }

    /**
     *
     * @param StoreProduct|null $product
     * @return static
     */
    public function setProduct(?StoreProduct $product): static
    {
        $this->product = $product;

        return $this;
    }

    /**
     *
     * @return StoreCategory|null
     */
    public function getCategory(): ?StoreCategory
    {
        return $this->category;
    }

    /**
     *
     * @param StoreCategory|null $category
     * @return static
     */
    public function setCategory(?StoreCategory $category): static
    {
        $this->category = $category;

        return $this;
    }
}
