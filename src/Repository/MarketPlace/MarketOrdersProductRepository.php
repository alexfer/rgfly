<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketOrders;
use App\Entity\MarketPlace\MarketOrdersProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketOrdersProduct>
 *
 * @method MarketOrdersProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketOrdersProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketOrdersProduct[]    findAll()
 * @method MarketOrdersProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketOrdersProductRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketOrdersProduct::class);
    }

    /**
     * @param string $session
     * @return array|null
     */
    public function summary(string $session): ?array
    {
        $qb = $this->createQueryBuilder('op')
            ->select([
                'o.id as order_id',
                'o.number',
                'o.total as order_total',
                'm.id as market_id',
                'm.name as market_name',
                'm.slug as market_slug',
                'op.quantity',
                'op.discount',
                'SUM(CASE WHEN op.discount > 0 THEN op.cost - ((op.quantity * op.cost) * op.discount)/100 ELSE op.quantity * op.cost end) as amount_discount',
                'SUM(op.cost * op.quantity) as amount',
            ])
            ->leftJoin(
                MarketOrders::class,
                'o',
                Expr\Join::WITH,
                'o.id = op.orders')
            ->leftJoin(Market::class,
                'm',
                Expr\Join::WITH, 'm.id = o.market')
            ->where('o.session = :session')
            ->setParameter('session', $session)
            ->groupBy('o.id, m.id, op.quantity, op.discount');

        return $qb->getQuery()->getResult();
    }

}
