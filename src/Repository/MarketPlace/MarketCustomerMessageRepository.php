<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCustomerMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCustomerMessage>
 *
 * @method MarketCustomerMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketCustomerMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketCustomerMessage[]    findAll()
 * @method MarketCustomerMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketCustomerMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCustomerMessage::class);
    }

//    /**
//     * @return MarketCustomerMessage[] Returns an array of MarketCustomerMessage objects
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

//    public function findOneBySomeField($value): ?MarketCustomerMessage
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
