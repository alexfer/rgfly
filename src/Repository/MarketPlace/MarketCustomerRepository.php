<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCustomer>
 *
 * @method MarketCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketCustomer[]    findAll()
 * @method MarketCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketCustomerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCustomer::class);
    }

}
