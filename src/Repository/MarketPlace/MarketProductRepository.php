<?php

namespace App\Repository\MarketPlace;

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
     * @param array $ids
     * @param int $limit
     * @return array|null
     */
    public function findProductsByParentCategory(array $ids, int $limit = 10): ?array
    {
        $qb = $this->createQueryBuilder('p')
            ->distinct()
            //->select(['p.slug'])
            ->leftJoin(MarketCategoryProduct::class, 'cp', Expr\Join::WITH, 'p.id = cp.product')
            ->leftJoin(MarketCategory::class, 'c', Expr\Join::WITH, 'cp.category = c.id')
            ->where('cp.category IN (:ids)')
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->andWhere('p.deleted_at IS NULL')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();

    }
}
