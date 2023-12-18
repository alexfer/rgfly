<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductAttach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductAttach>
 *
 * @method MarketProductAttach|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductAttach|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductAttach[]    findAll()
 * @method MarketProductAttach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductAttachRepository extends ServiceEntityRepository
{

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductAttach::class);
    }
}
