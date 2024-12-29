<?php

namespace Essence\Repository\MarketPlace;

use Essence\Entity\MarketPlace\StoreProductAttach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreProductAttach>
 *
 * @method StoreProductAttach|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreProductAttach|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreProductAttach[]    findAll()
 * @method StoreProductAttach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreProductAttachRepository extends ServiceEntityRepository
{

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreProductAttach::class);
    }
}
