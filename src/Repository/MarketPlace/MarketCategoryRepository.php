<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCategory>
 *
 * @method MarketCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketCategory[]    findAll()
 * @method MarketCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketCategoryRepository extends ServiceEntityRepository
{

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCategory::class);
    }
}
