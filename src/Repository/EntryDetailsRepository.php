<?php

namespace Inno\Repository;

use Inno\Entity\EntryDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EntryDetails>
 *
 * @method EntryDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntryDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntryDetails[]    findAll()
 * @method EntryDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryDetailsRepository extends ServiceEntityRepository
{

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntryDetails::class);
    }
}
