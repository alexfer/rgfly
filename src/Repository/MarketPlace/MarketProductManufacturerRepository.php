<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductManufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductManufacturer>
 *
 * @method MarketProductManufacturer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductManufacturer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductManufacturer[]    findAll()
 * @method MarketProductManufacturer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductManufacturerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductManufacturer::class);
    }

//    /**
//     * @return MarketProductManufacturer[] Returns an array of MarketProductManufacturer objects
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

//    public function findOneBySomeField($value): ?MarketProductManufacturer
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
