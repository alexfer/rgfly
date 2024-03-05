<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketWishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketWishlist>
 *
 * @method MarketWishlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketWishlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketWishlist[]    findAll()
 * @method MarketWishlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketWishlistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketWishlist::class);
    }

    //    /**
    //     * @return MarketWishlist[] Returns an array of MarketWishlist objects
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

    //    public function findOneBySomeField($value): ?MarketWishlist
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
