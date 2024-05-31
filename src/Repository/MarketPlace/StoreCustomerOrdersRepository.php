<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\StoreCustomerOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreCustomerOrders>
 *
 * @method StoreCustomerOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreCustomerOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreCustomerOrders[]    findAll()
 * @method StoreCustomerOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreCustomerOrdersRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreCustomerOrders::class);
    }

}
