<?php

namespace Inno\Repository\MarketPlace;

use Inno\Entity\MarketPlace\StoreProductManufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreProductManufacturer>
 *
 * @method StoreProductManufacturer|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreProductManufacturer|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreProductManufacturer[]    findAll()
 * @method StoreProductManufacturer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreProductManufacturerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreProductManufacturer::class);
    }

    /**
     * @param int $id
     * @return int|null
     */
    public function drop(int $id): ?int
    {
        return $this->createQueryBuilder('store_product_manufacturer')
            ->delete(StoreProductManufacturer::class, 'store_product_manufacturer')
            ->where('store_product_manufacturer.product is null')
            ->andWhere('store_product_manufacturer.manufacturer is null')
            ->andWhere('store_product_manufacturer.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
