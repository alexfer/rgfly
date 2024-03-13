<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductAttributeValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductAttributeValue>
 *
 * @method MarketProductAttributeValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductAttributeValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductAttributeValue[]    findAll()
 * @method MarketProductAttributeValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductAttributeValueRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductAttributeValue::class);
    }

}
