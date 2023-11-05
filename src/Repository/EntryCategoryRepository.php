<?php

namespace App\Repository;

use App\Entity\EntryCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EntryCategory>
 *
 * @method EntryCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntryCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntryCategory[]    findAll()
 * @method EntryCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntryCategory::class);
    }

//    /**
//     * @return EntryCategory[] Returns an array of EntryCategory objects
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

//    public function findOneBySomeField($value): ?EntryCategory
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
