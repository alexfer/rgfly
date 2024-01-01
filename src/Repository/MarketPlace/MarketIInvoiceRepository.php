<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketIInvoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketIInvoice>
 *
 * @method MarketIInvoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketIInvoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketIInvoice[]    findAll()
 * @method MarketIInvoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketIInvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketIInvoice::class);
    }

//    /**
//     * @return MarketIInvoice[] Returns an array of MarketIInvoice objects
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

//    public function findOneBySomeField($value): ?MarketIInvoice
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
