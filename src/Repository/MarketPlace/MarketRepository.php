<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Market>
 *
 * @method Market|null find($id, $lockMode = null, $lockVersion = null)
 * @method Market|null findOneBy(array $criteria, array $orderBy = null)
 * @method Market[]    findAll()
 * @method Market[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Market::class);
        $this->connection = $this->getEntityManager()->getConnection();
    }

    /**
     * @param string|null $query
     * @return array
     * @throws Exception
     */
    public function search(?string $query): array
    {
        $statement = $this->connection->prepare('select market_search(:query)');
        $statement->bindValue('query', $query, \PDO::PARAM_STR);
        $result = $statement->executeQuery()->fetchAllAssociative();
        return json_decode($result[0]['market_search'], true) ?: [];
    }


    /**
     * @param string|null $query
     * @param UserInterface $user
     * @return array
     * @throws Exception
     */
    public function searchByOwner(?string $query, UserInterface $user): array
    {
        $statement = $this->connection->prepare('select owner_market_search(:query, :oid)');
        $statement->bindValue('query', $query, \PDO::PARAM_STR);
        $statement->bindValue('oid', $user->getId(), \PDO::PARAM_INT);
        $result = $statement->executeQuery()->fetchAllAssociative();
        return json_decode($result[0]['owner_market_search'], true) ?: [];
    }

}
