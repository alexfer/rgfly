<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreCustomerOrders;
use App\Entity\MarketPlace\StoreOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreOrders>
 *
 * @method StoreOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreOrders[]    findAll()
 * @method StoreOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreOrdersRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreOrders::class);
    }

    /**
     * @param string $query
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function singleFetch(
        string $query,
        StoreCustomer $customer
    ): ?array
    {
        $qb = $this->createQueryBuilder('mo')
            ->select(['mo.id', 'mo.number', 'm.id as store'])
            ->join(Store::class, 'm', 'WITH', 'mo.store = m.id')
            ->leftJoin(StoreCustomerOrders::class, 'mco', 'WITH', 'mo.id = mco.orders')
            ->where('mco.customer = :customer')
            ->andWhere("LOWER(mo.number) LIKE :query")
            ->setParameter('query', '%' . strtolower($query) . '%')
            ->setParameter('customer', $customer);

        return $qb->getQuery()->getOneOrNullResult();
    }

}
