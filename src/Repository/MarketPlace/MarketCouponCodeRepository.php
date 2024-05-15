<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCouponCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCouponCode>
 */
class MarketCouponCodeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCouponCode::class);
    }
}
