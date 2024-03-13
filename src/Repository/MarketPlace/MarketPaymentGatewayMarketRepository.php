<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketPaymentGatewayMarket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketPaymentGatewayMarket>
 *
 * @method MarketPaymentGatewayMarket|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketPaymentGatewayMarket|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketPaymentGatewayMarket[]    findAll()
 * @method MarketPaymentGatewayMarket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketPaymentGatewayMarketRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketPaymentGatewayMarket::class);
    }


}
