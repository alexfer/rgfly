<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketAddress>
 *
 * @method MarketAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketAddress[]    findAll()
 * @method MarketAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketAddressRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketAddress::class);
    }

    /**
     * @param int $customer
     * @param array $values
     * @return int
     * @throws Exception
     */
    public function create(int $customer, array $values): int
    {
        $jsonValues = json_encode($values);

        $statement = $this->getEntityManager()
            ->getConnection()
            ->prepare('select create_address(:customer_id, :values)');
        $statement->bindValue('customer_id', $customer, \PDO::PARAM_INT);
        $statement->bindValue('values', $jsonValues, \PDO::PARAM_STR);

        return $statement->executeQuery()->fetchOne();
    }
}
