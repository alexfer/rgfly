<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketInvoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketInvoice>
 *
 * @method MarketInvoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketInvoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketInvoice[]    findAll()
 * @method MarketInvoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketInvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketInvoice::class);
    }

//    /**
//     * @return MarketInvoice[] Returns an array of MarketInvoice objects
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

//    public function findOneBySomeField($value): ?MarketInvoice
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
