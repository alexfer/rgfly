<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductProvider>
 *
 * @method MarketProductProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductProvider[]    findAll()
 * @method MarketProductProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductProvider::class);
    }

//    /**
//     * @return MarketProductProvider[] Returns an array of MarketProductProvider objects
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

//    public function findOneBySomeField($value): ?MarketProductProvider
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
