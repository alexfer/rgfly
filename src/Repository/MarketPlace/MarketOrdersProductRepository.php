<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketOrdersProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketOrdersProduct>
 *
 * @method MarketOrdersProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketOrdersProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketOrdersProduct[]    findAll()
 * @method MarketOrdersProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketOrdersProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketOrdersProduct::class);
    }

//    /**
//     * @return MarketOrdersProduct[] Returns an array of MarketOrdersProduct objects
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

//    public function findOneBySomeField($value): ?MarketOrdersProduct
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
