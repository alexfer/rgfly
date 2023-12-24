<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProvider>
 *
 * @method MarketProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProvider[]    findAll()
 * @method MarketProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProvider::class);
    }

//    /**
//     * @return MarketProvider[] Returns an array of MarketProvider objects
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

//    public function findOneBySomeField($value): ?MarketProvider
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
