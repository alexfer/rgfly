<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketManufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketManufacturer>
 *
 * @method MarketManufacturer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketManufacturer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketManufacturer[]    findAll()
 * @method MarketManufacturer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketManufacturerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketManufacturer::class);
    }

    /**
     * @param Market $market
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function manufacturers(
        Market  $market,
        ?string $search = null,
        int     $offset = 0,
        int     $limit = 10,
    ): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.market = :market')
            ->setParameter('market', $market)
            ->andWhere('LOWER(m.name) LIKE :search')
            ->setParameter('search', '%' . strtolower($search) . '%')
            ->setFirstResult($offset)->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }
}
