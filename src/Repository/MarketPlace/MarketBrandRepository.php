<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketBrand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketBrand>
 *
 * @method MarketBrand|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketBrand|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketBrand[]    findAll()
 * @method MarketBrand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketBrandRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketBrand::class);
    }

    /**
     * @param Market $market
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function brands(
        Market  $market,
        ?string $search = null,
        int     $offset = 0,
        int     $limit = 10,
    ): array
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.market = :market')
            ->setParameter('market', $market)
            ->andWhere('LOWER(b.name) LIKE :search')
            ->setParameter('search', '%' . strtolower($search) . '%')
            ->setFirstResult($offset)->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }
}
