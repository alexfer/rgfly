<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketSupplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketSupplier>
 *
 * @method MarketSupplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketSupplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketSupplier[]    findAll()
 * @method MarketSupplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketSupplierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketSupplier::class);
    }

//    /**
//     * @return MarketSupplier[] Returns an array of MarketSupplier objects
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

//    public function findOneBySomeField($value): ?MarketSupplier
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
