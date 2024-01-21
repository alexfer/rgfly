<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductAttributeValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductAttributeValue>
 *
 * @method MarketProductAttributeValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductAttributeValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductAttributeValue[]    findAll()
 * @method MarketProductAttributeValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductAttributeValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductAttributeValue::class);
    }

//    /**
//     * @return MarketProductAttributeValue[] Returns an array of MarketProductAttributeValue objects
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

//    public function findOneBySomeField($value): ?MarketProductAttributeValue
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
