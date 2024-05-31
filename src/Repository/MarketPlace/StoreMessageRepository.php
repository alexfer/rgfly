<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Statement;
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
        $statement->bindValue('offset', $offset, \PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
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
        Store  $store,
        ?string $priority = null,
        int     $offset = 0,
        int     $limit = 25,
    ): array
    {
        $statement = $this->connection->prepare('select get_messages(:store_id, :priority, :offset, :limit)');
        $statement->bindValue('store_id', $store->getId(), \PDO::PARAM_INT);
        $statement->bindValue('priority', $priority, \PDO::PARAM_STR);
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
        int     $offset = 0,
        int     $limit = 25,
    ): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare('select get_customer_messages(:customer_id, :offset, :limit)');
        $statement->bindValue('customer_id', $customer->getId(), \PDO::PARAM_INT);
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_customer_messages'], true) ?: [];
    }

}
