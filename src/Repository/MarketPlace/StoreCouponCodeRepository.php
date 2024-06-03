<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\StoreCouponCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreCouponCode>
 */
class StoreCouponCodeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreCouponCode::class);
    }
}
