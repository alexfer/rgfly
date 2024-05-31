<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\StoreCouponUsage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreCouponUsage>
 */
class StoreCouponUsageRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreCouponUsage::class);
    }
}
