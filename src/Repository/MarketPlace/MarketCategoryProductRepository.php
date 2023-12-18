<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCategoryProduct;
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
}
