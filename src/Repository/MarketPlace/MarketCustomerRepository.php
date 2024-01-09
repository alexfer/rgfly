<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCustomer>
 *
 * @method MarketCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketCustomer[]    findAll()
 * @method MarketCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketCustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCustomer::class);
    }

//    /**
//     * @return MarketCustomer[] Returns an array of MarketCustomer objects
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

//    public function findOneBySomeField($value): ?MarketCustomer
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
