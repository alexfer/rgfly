<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketManufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketManufacturer>
 *
 * @method MarketManufacturer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketManufacturer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketManufacturer[]    findAll()
 * @method MarketManufacturer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketManufacturerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketManufacturer::class);
    }

//    /**
//     * @return MarketManufacturer[] Returns an array of MarketManufacturer objects
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

//    public function findOneBySomeField($value): ?MarketManufacturer
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
