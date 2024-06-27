<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\{Store, StoreProduct};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\{Connection, Exception, Statement};
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreProduct>
 */
class StoreProductRepository extends ServiceEntityRepository
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreProduct::class);
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
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function fetchProducts(int $offset = 0, int $limit = 10): array
    {
        $statement = $this->connection->prepare('select get_products(:offset, :limit)');
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_products'], true) ?: [];
    }

    /**
     * @param string $slug
     * @return array
     * @throws Exception
     */
    public function fetchProduct(string $slug): array
    {
        $statement = $this->connection->prepare('select get_product(:slug)');
        $statement->bindValue('slug', $slug, \PDO::PARAM_STR);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_product'], true) ?: [];
    }

    /**
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function randomProducts(int $limit): array
    {
        $statement = $this->connection->prepare('select get_random_products(:rows_count)');
        $statement->bindValue('rows_count', $limit, \PDO::PARAM_STR);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_random_products'], true) ?: [];
    }

    /**
     * @param int $id
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function findProductsByChildCategory(
        int $id,
        int $offset = 0,
        int $limit = 10,
    ): array
    {
        $statement = $this->connection->prepare('select get_products_by_child_category(:child_id, :offset, :limit)');
        $statement->bindValue('child_id', $id, \PDO::PARAM_INT);
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_products_by_child_category'], true) ?: [];
    }


    /**
     * @param string $slug
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function findProductsByParentCategory(
        string $slug,
        int    $offset = 0,
        int    $limit = 10,
    ): array
    {
        $statement = $this->connection->prepare('select get_products_by_parent_category(:slug, :offset, :limit)');
        $statement->bindValue('slug', $slug, \PDO::PARAM_STR);
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_products_by_parent_category'], true) ?: [];
    }

    /**
     * @param Store $store
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function products(
        Store   $store,
        ?string $search = null,
        int     $offset = 0,
        int     $limit = 10,
    ): array
    {
        $statement = $this->connection->prepare('select backdrop_products(:store_id, :query, :offset, :limit)');
        $statement->bindValue('store_id', $store->getId(), \PDO::PARAM_INT);
        $statement->bindValue('query', $search ?: '', \PDO::PARAM_STR);
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['backdrop_products'], true) ?: [];
    }

}
