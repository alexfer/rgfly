<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductSupplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductSupplier>
 *
 * @method MarketProductSupplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductSupplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductSupplier[]    findAll()
 * @method MarketProductSupplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductSupplierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductSupplier::class);
    }

//    /**
//     * @return MarketProductSupplier[] Returns an array of MarketProductSupplier objects
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

//    public function findOneBySomeField($value): ?MarketProductSupplier
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
