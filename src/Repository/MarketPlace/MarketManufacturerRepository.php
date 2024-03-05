<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketManufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketManufacturer>
 *
 * @method MarketManufacturer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketManufacturer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketManufacturer[]    findAll()
 * @method MarketManufacturer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketManufacturerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketManufacturer::class);
    }

}
