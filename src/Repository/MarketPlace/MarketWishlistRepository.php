<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketWishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketWishlist>
 *
 * @method MarketWishlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketWishlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketWishlist[]    findAll()
 * @method MarketWishlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketWishlistRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketWishlist::class);
    }

}
