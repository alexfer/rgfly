<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreBrand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreBrand>
 *
 * @method StoreBrand|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreBrand|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreBrand[]    findAll()
 * @method StoreBrand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreBrandRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreBrand::class);
    }

    /**
     * @param Store $store
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function brands(
        Store   $store,
        ?string $search = null,
        int     $offset = 0,
        int     $limit = 10,
    ): array
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.store = :store')
            ->setParameter('store', $store)
            ->andWhere('LOWER(b.name) LIKE :search')
            ->setParameter('search', '%' . strtolower($search) . '%')
            ->setFirstResult($offset)->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }
}
