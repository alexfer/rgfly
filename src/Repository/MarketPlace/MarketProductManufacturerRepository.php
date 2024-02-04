<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductManufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductManufacturer>
 *
 * @method MarketProductManufacturer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductManufacturer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductManufacturer[]    findAll()
 * @method MarketProductManufacturer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductManufacturerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductManufacturer::class);
    }

    /**
     * @param int $id
     * @return int|null
     */
    public function drop(int $id): ?int
    {
        return $this->createQueryBuilder('market_product_manufacturer')
            ->delete(MarketProductManufacturer::class, 'market_product_manufacturer')
            ->where('market_product_manufacturer.product is null')
            ->andWhere('market_product_manufacturer.manufacturer is null')
            ->andWhere('market_product_manufacturer.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
