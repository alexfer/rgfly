<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductBrand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductBrand>
 *
 * @method MarketProductBrand|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductBrand|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductBrand[]    findAll()
 * @method MarketProductBrand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductBrandRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductBrand::class);
    }

    /**
     * @param int $id
     * @return int|null
     */
    public function drop(int $id): ?int
    {
        return $this->createQueryBuilder('market_product_brand')
            ->delete(MarketProductBrand::class, 'market_product_brand')
            ->where('market_product_brand.product is null')
            ->andWhere('market_product_brand.brand is null')
            ->andWhere('market_product_brand.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
