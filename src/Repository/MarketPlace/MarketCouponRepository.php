<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCoupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketCoupon>
 */
class MarketCouponRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketCoupon::class);
    }

    /**
     * @param Statement $statement
     * @param int $offset
     * @param int $limit
     * @return Statement
     * @throws Exception
     */
    private function bindPagination(
        Statement $statement,
        int       $offset,
        int       $limit,
    ): Statement
    {
        $statement->bindValue('offset', $offset, \PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        return $statement;
    }


    /**
     * @param Market $market
     * @param string|null $type
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function fetch(
        Market  $market,
        ?string $type = null,
        int     $offset = 0,
        int     $limit = 25,
    ): array
    {
        $coupons = [];

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare('select get_coupons(:market_id, :type, :offset, :limit)');
        $statement->bindValue('market_id', $market->getId(), \PDO::PARAM_INT);
        $statement->bindValue('type', $type, \PDO::PARAM_STR);
        $statement = $this->bindPagination($statement, $offset, $limit);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_coupons'], true) ?: $coupons;
    }

    /**
     * @param Market $market
     * @param string $type
     * @param int $event
     * @return array|null
     * @throws Exception
     */
    public function getSingleActive(
        Market $market,
        string $type,
        int    $event = 1,
    )
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare('select get_active_coupon(:market_id, :type, :event)');
        $statement->bindValue('market_id', $market->getId(), \PDO::PARAM_INT);
        $statement->bindValue('type', $type, \PDO::PARAM_STR);
        $statement->bindValue('event', $event, \PDO::PARAM_INT);
        $result = $statement->executeQuery()->fetchAllAssociative();

        return json_decode($result[0]['get_active_coupon'], true) ?? $result;
    }

    /**
     * @param Market $market
     * @param string|null $type
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function fetchActive(
        Market  $market,
        ?string $type = null,
        int     $offset = 0,
        int     $limit = 10
    ): array
    {
        $qb = $this->createQueryBuilder('mc')
            ->select([
                'mc.id',
                'mc.name',
            ])
            ->where('mc.expired_at > :date')
            ->andWhere('mc.market = :market')
            ->andWhere('mc.type = :type')
            ->setParameter('market', $market)
            ->setParameter('date', date('Y-m-d'))
            ->setParameter('type', $type)
            ->orderBy('mc.expired_at', 'asc')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
