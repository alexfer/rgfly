<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketCustomerOrders;
use App\Entity\MarketPlace\MarketOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketOrders>
 *
 * @method MarketOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketOrders[]    findAll()
 * @method MarketOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketOrdersRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketOrders::class);
    }

    /**
     * @param string $query
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function singleFetch(
        string $query,
        MarketCustomer $customer
    ): ?array
    {
        $qb = $this->createQueryBuilder('mo')
            ->select(['mo.id', 'mo.number', 'm.id as market'])
            ->join(Market::class, 'm', 'WITH', 'mo.market = m.id')
            ->leftJoin(MarketCustomerOrders::class, 'mco', 'WITH', 'mo.id = mco.orders')
            ->where('mco.customer = :customer')
            ->andWhere("LOWER(mo.number) LIKE :query")
            ->setParameter('query', '%' . strtolower($query) . '%')
            ->setParameter('customer', $customer);

        return $qb->getQuery()->getOneOrNullResult();
    }

}
