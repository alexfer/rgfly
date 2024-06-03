<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreManufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreManufacturer>
 *
 * @method StoreManufacturer|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreManufacturer|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreManufacturer[]    findAll()
 * @method StoreManufacturer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreManufacturerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreManufacturer::class);
    }

    /**
     * @param Store $store
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function manufacturers(
        Store  $store,
        ?string $search = null,
        int     $offset = 0,
        int     $limit = 10,
    ): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.store = :store')
            ->setParameter('store', $store)
            ->andWhere('LOWER(m.name) LIKE :search')
            ->setParameter('search', '%' . strtolower($search) . '%')
            ->setFirstResult($offset)->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }
}
