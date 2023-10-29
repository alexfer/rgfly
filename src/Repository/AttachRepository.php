<?php

namespace App\Repository;

use App\Entity\Attach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attach>
 *
 * @method Attach|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attach|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attach[]    findAll()
 * @method Attach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttachRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attach::class);
    }
}
