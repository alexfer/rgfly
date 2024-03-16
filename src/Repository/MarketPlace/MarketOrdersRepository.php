<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketOrders>
 *
 * @method MarketOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketOrders[]    findAll()
 * @method MarketOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketOrdersRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketOrders::class);
    }

}
