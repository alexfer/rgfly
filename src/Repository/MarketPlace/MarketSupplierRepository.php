<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketSupplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketSupplier>
 *
 * @method MarketSupplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketSupplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketSupplier[]    findAll()
 * @method MarketSupplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketSupplierRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketSupplier::class);
    }

    /**
     * @param Market $market
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function suppliers(
        Market  $market,
        ?string $search = null,
        int     $offset = 0,
        int     $limit = 10,
    ): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.market = :market')
            ->setParameter('market', $market)
            ->andWhere('LOWER(s.name) LIKE :search')
            ->setParameter('search', '%' . strtolower($search) . '%')
            ->setFirstResult($offset)->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }

}
