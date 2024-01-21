<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductAttribute>
 *
 * @method MarketProductAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductAttribute[]    findAll()
 * @method MarketProductAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductAttribute::class);
    }

//    /**
//     * @return MarketProductAttribute[] Returns an array of MarketProductAttribute objects
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

//    public function findOneBySomeField($value): ?MarketProductAttribute
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
