<?php declare(strict_types=1);

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

    /**
     * @param mixed $id
     * @return StorePaymentGateway|null
     */
    public function fetch(mixed $id)
    {
        $qb = $this->createQueryBuilder('spg')
            ->select([
                'spg.id',
                'spg.name',
                'spg.summary',
                'spg.active',
                'spg.icon',
                'spg.slug',
                'spg.handler_text as handlerText',
            ])
            ->where("spg.id = :id")
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
