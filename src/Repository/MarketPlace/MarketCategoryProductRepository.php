<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCategoryProduct;
use App\Entity\MarketPlace\MarketProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCategoryProduct>
 *
 * @method MarketCategoryProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketCategoryProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketCategoryProduct[]    findAll()
 * @method MarketCategoryProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketCategoryProductRepository extends ServiceEntityRepository
{

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCategoryProduct::class);
    }

    /**
     * @param MarketProduct $product
     * @return mixed
     */
    public function removeCategoryProduct(MarketProduct $product): mixed
    {
        return $this->createQueryBuilder('cp')
            ->delete()
            ->where('cp.product = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->execute();
    }
}
