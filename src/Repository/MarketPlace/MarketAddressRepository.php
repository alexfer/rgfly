<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketAddress>
 *
 * @method MarketAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketAddress[]    findAll()
 * @method MarketAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketAddress::class);
    }

    //    /**
    //     * @return MarketAddress[] Returns an array of MarketAddress objects
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

    //    public function findOneBySomeField($value): ?MarketAddress
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
