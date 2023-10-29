<?php

namespace App\Repository;

use App\Entity\EntryAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EntryAttachment>
 *
 * @method EntryAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntryAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntryAttachment[]    findAll()
 * @method EntryAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntryAttachment::class);
    }

//    /**
//     * @return EntryAttachment[] Returns an array of EntryAttachment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EntryAttachment
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
