<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketCategoryProduct;
use App\Entity\MarketPlace\MarketProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
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
    private string $sql;

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProduct::class);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function getProductsFromSql(int $offset = 0, int $limit = 10): array
    {
        $products = [];

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare('select get_products(?, ?)');
        $stmt->bindValue(1, $offset);
        $stmt->bindValue(2, $limit);

        $query = $stmt->executeQuery();
        $result = $query->fetchAllAssociative();

        return json_decode($result[0]['get_products'], true) ?: $products;
    }

    /**
     * @param int $id
     * @param int $limit
     * @return array|null
     */
    public function findProductsByChildrenCategory(int $id, int $limit = 10): ?array
    {
        $qb = $this->createQueryBuilder('p')
            ->distinct()
            ->join(MarketCategoryProduct::class, 'cp', Expr\Join::WITH, 'p.id = cp.product')
            ->join(MarketCategory::class, 'c', Expr\Join::WITH, 'cp.category = c.id')
            ->leftJoin(MarketCategory::class, 'cc', Expr\Join::WITH, 'c.parent = cc.id')
            ->join(Market::class, 'm', Expr\Join::WITH, 'p.market = m.id')
            ->where('cp.category = :id')
            ->setParameter('id', $id)
            ->andWhere('p.deleted_at IS NULL')
            ->setMaxResults($limit)
            ->setCacheable(true)
            ->setCacheMode('p.id');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $ids
     * @param int $limit
     * @return array|null
     */
    public function findProductsByParentCategory(array $ids, int $limit = 10): ?array
    {
        $qb = $this->createQueryBuilder('p')
            ->distinct()
            ->join(MarketCategoryProduct::class, 'cp', Expr\Join::WITH, 'p.id = cp.product')
            ->join(MarketCategory::class, 'c', Expr\Join::WITH, 'cp.category = c.id')
            ->join(Market::class, 'm', Expr\Join::WITH, 'p.market = m.id')
            ->where('cp.category IN (:ids)')
            ->setParameter('ids', $ids)
            ->andWhere('p.deleted_at IS NULL')
            ->setMaxResults($limit)
            ->setCacheable(true)
            ->setCacheMode('p.id');

        return $qb->getQuery()->getResult();

    }
}
