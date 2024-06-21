<?php

namespace App\Service\MarketPlace\Dashboard\Category\Interface;

use App\Entity\MarketPlace\StoreCategoryProduct;
use App\Repository\MarketPlace\StoreCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormInterface;

interface ServeInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function handle(array $criteria = [], array $orderBy = []): array;

    /**
     * @param FormInterface $form
     * @return mixed
     */
    public function request(FormInterface $form): mixed;

    /**
     * @return StoreCategoryRepository|EntityRepository
     */
    public function repository(): StoreCategoryRepository|EntityRepository;

    /**
     * @return StoreCategoryProduct|EntityRepository
     */
    public function categoryProductRepository(): StoreCategoryProduct|EntityRepository;
}