<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\StoreCustomerOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\{Connection, Exception};
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreCustomerOrders>
 *
 * @method StoreCustomerOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreCustomerOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreCustomerOrders[]    findAll()
 * @method StoreCustomerOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreCustomerOrdersRepository extends ServiceEntityRepository
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreCustomerOrders::class);
        $this->connection = $this->getEntityManager()->getConnection();
    }

    /**
     * @param int $customerId
     * @param int $offset
     * @param int $limit
     * @return array|null
     * @throws Exception
     */
    public function getCustomerOrders(
        int $customerId,
        int $offset = 0,
        int $limit = 25,
    ): ?array
    {
        $statement = $this->connection->prepare('select get_customer_orders(:customer_id, :offset, :limit)');
        $statement->bindValue('customer_id', $customerId, \PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, \PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_customer_orders'], true) ?: [];
    }

    /**
     * @param array $ids
     * @return string|null
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function summaryCustomers(array $ids): array
    {
        $qb = $this->createQueryBuilder('co')
            //->distinct()
            ->select('count(co.id) as total')
            ->where('co.orders IN (:ids)')
            ->setParameter('ids', $ids)
            ->groupBy('co.customer');

        return $qb->getQuery()->getResult();
    }

}
