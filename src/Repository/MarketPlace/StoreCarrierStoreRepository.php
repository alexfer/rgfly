<?php declare(strict_types=1);

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\StoreCarrierStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreCarrierStore>
 */
class StoreCarrierStoreRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreCarrierStore::class);
    }
}
