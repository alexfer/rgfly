<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductSupplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductSupplier>
 *
 * @method MarketProductSupplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductSupplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductSupplier[]    findAll()
 * @method MarketProductSupplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductSupplierRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductSupplier::class);
    }

    /**
     * @param int $id
     * @return int|null
     */
    public function drop(int $id): ?int
    {
        return $this->createQueryBuilder('market_product_supplier')
            ->delete(MarketProductSupplier::class, 'market_product_supplier')
            ->where('market_product_supplier.product is null')
            ->andWhere('market_product_supplier.supplier is null')
            ->andWhere('market_product_supplier.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
