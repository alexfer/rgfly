<?php declare(strict_types=1);

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\{Store, StoreCustomer, StoreMessage};
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\{Connection, Exception, Statement};
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreMessage>
 */
class StoreMessageRepository extends ServiceEntityRepository
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
        parent::__construct($registry, StoreMessage::class);
        $this->connection = $this->getEntityManager()->getConnection();
    }

    /**
     * @param Statement $statement
     * @param int $offset
     * @param int $limit
     * @return Statement
     * @throws Exception
     */
    private function bindPagination(
        Statement $statement,
        int       $offset,
        int       $limit,
    ): Statement
    {
        $statement->bindValue('offset', $offset);
        $statement->bindValue('limit', $limit);
        return $statement;
    }

    /**
     * @param Store $store
     * @param string|null $priority
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function fetchAll(
        Store   $store,
        ?string $priority = null,
        int     $offset = 0,
        int     $limit = 25,
    ): array
    {
        $statement = $this->connection->prepare('select get_messages(:store_id, :priority, :offset, :limit)');
        $statement->bindValue('store_id', $store->getId());
        $statement->bindValue('priority', $priority);
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_messages'], true) ?: [];
    }

    /**
     * @param StoreCustomer $customer
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function fetchByCustomer(
        StoreCustomer $customer,
        int           $offset = 0,
        int           $limit = 25,
    ): array
    {
        $statement = $this->connection->prepare('select get_customer_messages(:customer_id, :offset, :limit)');
        $statement->bindValue('customer_id', $customer->getId());
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_customer_messages'], true) ?: [];
    }

    /**
     * @param User $user
     * @return int
     */
    public function countMessages(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.owner = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
