<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreSupplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreSupplier>
 *
 * @method StoreSupplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreSupplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreSupplier[]    findAll()
 * @method StoreSupplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreSupplierRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreSupplier::class);
    }

    /**
     * @param Store $store
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function suppliers(
        Store  $store,
        ?string $search = null,
        int     $offset = 0,
        int     $limit = 10,
    ): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.store = :store')
            ->setParameter('store', $store)
            ->andWhere('LOWER(s.name) LIKE :search')
            ->setParameter('search', '%' . strtolower($search) . '%')
            ->setFirstResult($offset)->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }

}
