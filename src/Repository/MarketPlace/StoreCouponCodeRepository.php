<?php

namespace App\Repository\MarketPlace;

use Doctrine\ORM\NonUniqueResultException;
use App\Entity\MarketPlace\{StoreCoupon, StoreCouponCode, StoreCouponUsage};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    /**
     * @param StoreCoupon $coupon
     * @param string $code
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function verify(StoreCoupon $coupon, string $code): mixed
    {
        $qb = $this->createQueryBuilder('scc');
        $qb->select(['scc.code'])
            ->join(StoreCouponUsage::class, 'scu', Join::WITH, 'scc.id != scu.coupon_code')
            ->where('scc.code = :code')
            ->andWhere('scc.coupon = :coupon')
            ->setParameter('code', $code)
            ->setParameter('coupon', $coupon);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
