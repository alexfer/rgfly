<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCouponUsage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCouponUsage>
 */
class MarketCouponUsageRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCouponUsage::class);
    }
}
