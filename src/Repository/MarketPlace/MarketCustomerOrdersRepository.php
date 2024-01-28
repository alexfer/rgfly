<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCustomerOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCustomerOrders>
 *
 * @method MarketCustomerOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketCustomerOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketCustomerOrders[]    findAll()
 * @method MarketCustomerOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketCustomerOrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCustomerOrders::class);
    }

//    /**
//     * @return MarketCustomerOrders[] Returns an array of MarketCustomerOrders objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MarketCustomerOrdres
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
