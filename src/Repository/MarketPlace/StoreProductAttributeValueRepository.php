<?php

namespace Essence\Repository\MarketPlace;

use Essence\Entity\MarketPlace\StoreProductAttributeValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreProductAttributeValue>
 *
 * @method StoreProductAttributeValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreProductAttributeValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreProductAttributeValue[]    findAll()
 * @method StoreProductAttributeValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreProductAttributeValueRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreProductAttributeValue::class);
    }

}
