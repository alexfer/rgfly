<?php declare(strict_types=1);

namespace App\Repository\MarketPlace;

use App\Entity\Attach;
use App\Entity\MarketPlace\StoreCarrier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreCarrier>
 */
class StoreCarrierRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreCarrier::class);
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function fetch(mixed $id): mixed
    {
        $qb = $this->createQueryBuilder('sc')
            ->select([
                'sc.id',
                'sc.name',
                'sc.slug',
                'sc.description',
                'sc.link_url as linkUrl',
                'sc.is_enabled as enabled',
                'a.name as image',

            ])
            ->leftJoin(Attach::class, 'a', Join::WITH, 'a.id = sc.attach')
            ->where("sc.id = :id")
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

}
