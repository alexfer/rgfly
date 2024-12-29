<?php

namespace Essence\Service\MarketPlace\Dashboard\Category;

use Essence\Entity\MarketPlace\StoreCategory;
use Essence\Entity\MarketPlace\StoreCategoryProduct;
use Essence\Repository\MarketPlace\StoreCategoryRepository;
use Essence\Service\MarketPlace\Dashboard\Category\Interface\ServeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormInterface;

readonly class Serve implements ServeInterface
{

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {

    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function handle(array $criteria = [], array $orderBy = []): array
    {
        return $this->repository()->findBy($criteria, $orderBy);
    }

    /**
     * @param FormInterface $form
     * @return mixed
     */
    public function request(FormInterface $form): mixed
    {
        return $form->get('category')->getData();
    }

    /**
     * @return StoreCategoryRepository|EntityRepository
     */
    public function repository(): StoreCategoryRepository|EntityRepository
    {
        return $this->em->getRepository(StoreCategory::class);
    }

    /**
     * @return StoreCategoryProduct|EntityRepository
     */
    public function categoryProductRepository(): StoreCategoryProduct|EntityRepository
    {
        return $this->em->getRepository(StoreCategoryProduct::class);
    }
}