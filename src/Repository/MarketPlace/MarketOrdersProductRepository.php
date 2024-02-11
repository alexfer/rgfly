<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketOrdersProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketOrdersProduct>
 *
 * @method MarketOrdersProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketOrdersProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketOrdersProduct[]    findAll()
 * @method MarketOrdersProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketOrdersProductRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketOrdersProduct::class);
    }

}
