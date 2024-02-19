<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketPaymentGatewayMarket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketPaymentGatewayMarket>
 *
 * @method MarketPaymentGatewayMarket|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketPaymentGatewayMarket|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketPaymentGatewayMarket[]    findAll()
 * @method MarketPaymentGatewayMarket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketPaymentGatewayMarketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketPaymentGatewayMarket::class);
    }

//    /**
//     * @return MarketPaymentGatewayMarket[] Returns an array of MarketPaymentGatewayMarket objects
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

//    public function findOneBySomeField($value): ?MarketPaymentGatewayMarket
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
