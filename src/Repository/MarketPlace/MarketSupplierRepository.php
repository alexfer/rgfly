<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketSupplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketSupplier>
 *
 * @method MarketSupplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketSupplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketSupplier[]    findAll()
 * @method MarketSupplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketSupplierRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketSupplier::class);
    }

}
