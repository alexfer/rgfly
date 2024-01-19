<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketCategoryProduct;
use App\Entity\MarketPlace\MarketProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
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
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProduct::class);
    }

    /**
     * @return string[]
     */
    private function columns(): array
    {
        return [
            'p.id',
            'p.slug',
            'p.cost',
            'p.name',
            'p.short_name',
            'c.name as category_name',
            'c.slug as category_slug',
            'm.name as market',
            'm.phone',
            'm.id as market_id',
            'm.currency',
            'm.slug as market_slug',
        ];
    }

    /**
     * @param int $limit
     * @return array|null
     */
    public function getProducts(int $limit = 10): ?array
    {
        $select = $this->columns();
        array_push($select, 'cc.name as parent_category_name', 'cc.slug as parent_category_slug');

        $qb = $this->createQueryBuilder('p')
            ->distinct()
            ->select($select)
            ->join(MarketCategoryProduct::class, 'cp', Expr\Join::WITH, 'p.id = cp.product')
            ->join(MarketCategory::class, 'c', Expr\Join::WITH, 'cp.category = c.id')
            ->leftJoin(MarketCategory::class, 'cc', Expr\Join::WITH, 'c.parent = cc.id')
            ->join(Market::class, 'm', Expr\Join::WITH, 'p.market = m.id')
            ->andWhere('p.deleted_at IS NULL')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $id
     * @param int $limit
     * @return array|null
     */
    public function findProductsByChildrenCategory(int $id, int $limit = 10): ?array
    {
        $select = $this->columns();
        array_push($select, 'cc.name as parent_category_name', 'cc.slug as parent_category_slug');

        $qb = $this->createQueryBuilder('p')
            ->distinct()
            ->select($select)
            ->join(MarketCategoryProduct::class, 'cp', Expr\Join::WITH, 'p.id = cp.product')
            ->join(MarketCategory::class, 'c', Expr\Join::WITH, 'cp.category = c.id')
            ->leftJoin(MarketCategory::class, 'cc', Expr\Join::WITH, 'c.parent = cc.id')
            ->join(Market::class, 'm', Expr\Join::WITH, 'p.market = m.id')
            ->where('cp.category = :id')
            ->setParameter('id', $id)
            ->andWhere('p.deleted_at IS NULL')
            ->setMaxResults($limit);

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
            ->select($this->columns())
            ->join(MarketCategoryProduct::class, 'cp', Expr\Join::WITH, 'p.id = cp.product')
            ->join(MarketCategory::class, 'c', Expr\Join::WITH, 'cp.category = c.id')
            ->join(Market::class, 'm', Expr\Join::WITH, 'p.market = m.id')
            ->where('cp.category IN (:ids)')
            ->setParameter('ids', $ids)
            ->andWhere('p.deleted_at IS NULL')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();

    }
}
