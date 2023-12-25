<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketProvider>
 *
 * @method MarketProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketProvider[]    findAll()
 * @method MarketProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketProviderRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketProvider::class);
    }
}
