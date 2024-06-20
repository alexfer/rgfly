<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\{Store, StoreCustomer};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\{Connection, Exception};
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Store>
 */
class StoreRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Store::class);
        $this->connection = $this->getEntityManager()->getConnection();
    }

    /**
     * @param Store $store
     * @return array|null
     * @throws Exception
     */
    public function extra(Store $store): ?array
    {
        $statement = $this->connection->prepare('select backdrop_store_extra(:store_id)');
        $statement->bindValue('store_id', $store->getId(), \PDO::PARAM_INT);
        $result = $statement->executeQuery()->fetchOne();
        return json_decode($result, true) ?: [];
    }

    /**
     * @param string|null $query
     * @return array
     * @throws Exception
     */
    public function search(?string $query): array
    {
        $statement = $this->connection->prepare('select store_search(:query)');
        $statement->bindValue('query', $query, \PDO::PARAM_STR);
        $result = $statement->executeQuery()->fetchAllAssociative();
        return json_decode($result[0]['store_search'], true) ?: [];
    }


    /**
     * @param string|null $query
     * @param UserInterface $user
     * @return array
     * @throws Exception
     */
    public function searchByOwner(?string $query, UserInterface $user): array
    {
        $statement = $this->connection->prepare('select owner_store_search(:query, :oid)');
        $statement->bindValue('query', $query, \PDO::PARAM_STR);
        $statement->bindValue('oid', $user->getId(), \PDO::PARAM_INT);
        $result = $statement->executeQuery()->fetchAllAssociative();
        return json_decode($result[0]['owner_store_search'], true) ?: [];
    }

    /**
     * @param string $slug
     * @param StoreCustomer|null $customer
     * @param int $offset
     * @param int $limit
     * @return array|null
     * @throws Exception
     */
    public function fetch(string $slug, ?StoreCustomer $customer, int $offset = 0, int $limit = 12): ?array
    {
        $statement = $this->connection->prepare('select get_store(:slug, :customer_id, :offset, :limit)');
        $statement->bindValue('slug', $slug, \PDO::PARAM_STR);
        $statement->bindValue('customer_id', $customer?->getId(), \PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, \PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);

        $result = $statement->executeQuery()->fetchAllAssociative();
        return json_decode($result[0]['get_store'], true) ?: null;
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function random(): ?array
    {
        $statement = $this->connection->prepare('select get_random_store()');
        $result = $statement->executeQuery()->fetchAllAssociative();
        return json_decode($result[0]['get_random_store'], true) ?: null;
    }

}
