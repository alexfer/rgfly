<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCoupon;
use App\Entity\MarketPlace\MarketProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProduct>
 *
 * @method MarketProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProduct[]    findAll()
 * @method MarketProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductRepository extends ServiceEntityRepository
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
        parent::__construct($registry, MarketProduct::class);
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
        $products = [];

        $statement = $this->connection->prepare('select get_products(:offset, :limit)');
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_products'], true) ?: $products;
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
        $products = [];

        $statement = $this->connection->prepare('select get_products_by_child_category(:child_id, :offset, :limit)');
        $statement->bindValue('child_id', $id, \PDO::PARAM_INT);
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_products_by_child_category'], true) ?: $products;
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
        $products = [];

        $statement = $this->connection->prepare('select get_products_by_parent_category(:slug, :offset, :limit)');
        $statement->bindValue('slug', $slug, \PDO::PARAM_STR);
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_products_by_parent_category'], true) ?: $products;
    }

    /**
     * @param Market $market
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function products(
        Market  $market,
        ?string $search = null,
        int     $offset = 0,
        int     $limit = 10,
    ): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin(MarketCoupon::class, 'mc', Expr\Join::WITH, 'mc.market = :market')
            ->where('p.market = :market')
            ->setParameter('market', $market)
            ->andWhere('LOWER(p.name) LIKE :search')
            ->setParameter('search', '%' . strtolower($search) . '%')
            ->setFirstResult($offset)->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }

}
