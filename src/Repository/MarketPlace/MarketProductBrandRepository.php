<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductBrand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductBrand>
 *
 * @method MarketProductBrand|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductBrand|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductBrand[]    findAll()
 * @method MarketProductBrand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductBrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductBrand::class);
    }

//    /**
//     * @return MarketProductBrand[] Returns an array of MarketProductBrand objects
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

//    public function findOneBySomeField($value): ?MarketProductBrand
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
