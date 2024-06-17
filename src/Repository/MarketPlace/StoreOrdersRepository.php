<?php

namespace App\Repository\MarketPlace;

use Doctrine\ORM\AbstractQuery;
use App\Entity\MarketPlace\{Store, StoreCustomer, StoreCustomerOrders, StoreOrders};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
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
     * @param StoreOrders $order
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function order(StoreOrders $order): ?array
    {
        $qb = $this->createQueryBuilder('o')
            ->select(['o.id', 'o.number', 'o.session'])
            ->where('o.id = :id')
            ->setParameter('id', $order->getId());

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param string $query
     * @param StoreCustomer $customer
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function singleFetch(
        string        $query,
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

    /**
     * @param string $sessionId
     * @param StoreCustomer|null $customer
     * @return array|null
     * @throws Exception
     */
    public function collection(string $sessionId, ?StoreCustomer $customer = null): ?array
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare('select get_order_summary(:session, :customer_id)');
        $statement->bindValue('session', $sessionId, \PDO::PARAM_STR);
        $statement->bindValue('customer_id', $customer?->getId(), \PDO::PARAM_INT);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_order_summary'], true) ?: [];
    }

}
