<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketCustomerMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCustomerMessage>
 *
 * @method MarketCustomerMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketCustomerMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketCustomerMessage[]    findAll()
 * @method MarketCustomerMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketCustomerMessageRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCustomerMessage::class);
    }

}
