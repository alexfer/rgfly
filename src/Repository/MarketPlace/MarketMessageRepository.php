<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Statement;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketMessage>
 */
class MarketMessageRepository extends ServiceEntityRepository
{

    private Connection $connection;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketMessage::class);
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
     * @param Market $market
     * @param string|null $priority
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function fetch(
        Market  $market,
        ?string $priority = null,
        int     $offset = 0,
        int     $limit = 25,
    ): array
    {
        $messages = [];

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare('select get_messages(:market_id, :priority, :offset, :limit)');
        $statement->bindValue('market_id', $market->getId(), \PDO::PARAM_INT);
        $statement->bindValue('priority', $priority, \PDO::PARAM_STR);
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_messages'], true) ?: $messages;
    }

}
