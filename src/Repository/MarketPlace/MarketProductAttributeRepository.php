<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductAttribute>
 *
 * @method MarketProductAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductAttribute[]    findAll()
 * @method MarketProductAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductAttributeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductAttribute::class);
    }

}
