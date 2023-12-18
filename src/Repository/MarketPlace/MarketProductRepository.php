<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProduct>
 *
 * @method MarketProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProduct[]    findAll()
 * @method MarketProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductRepository extends ServiceEntityRepository
{

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProduct::class);
    }
}
