<?php

namespace Essence\Repository\MarketPlace;

use Essence\Entity\MarketPlace\{StoreCouponUsage, StoreCustomer};
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

    /**
     * @param int $relation
     * @param StoreCustomer $customer
     * @return array
     */
    public function getCouponUsages(int $relation, StoreCustomer $customer): array
    {
        $qb = $this->createQueryBuilder('scu')
            ->select('scu')
            ->where('scu.customer = :user')
            ->setParameter('user', $customer)
            ->andWhere('scu.relation = :relation')
            ->setParameter('relation', $relation);

        return $qb->getQuery()->getResult();
    }
}
