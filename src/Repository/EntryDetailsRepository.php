<?php

namespace App\Repository;

use App\Entity\EntryDetailsOld;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EntryDetailsOld>
 *
 * @method EntryDetailsOld|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntryDetailsOld|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntryDetailsOld[]    findAll()
 * @method EntryDetailsOld[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryDetailsRepository extends ServiceEntityRepository
{

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntryDetailsOld::class);
    }
}
