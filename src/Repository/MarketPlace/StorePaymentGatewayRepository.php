<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\StorePaymentGateway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StorePaymentGateway>
 *
 * @method StorePaymentGateway|null find($id, $lockMode = null, $lockVersion = null)
 * @method StorePaymentGateway|null findOneBy(array $criteria, array $orderBy = null)
 * @method StorePaymentGateway[]    findAll()
 * @method StorePaymentGateway[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StorePaymentGatewayRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorePaymentGateway::class);
    }
}
