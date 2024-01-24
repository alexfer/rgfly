<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProductVariants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProductVariants>
 *
 * @method MarketProductVariants|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProductVariants|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProductVariants[]    findAll()
 * @method MarketProductVariants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProductVariantsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProductVariants::class);
    }

}
