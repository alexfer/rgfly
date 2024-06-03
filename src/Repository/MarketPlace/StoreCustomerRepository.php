<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\StoreCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StoreCustomer>
 *
 * @method StoreCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreCustomer[]    findAll()
 * @method StoreCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreCustomerRepository extends ServiceEntityRepository
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreCustomer::class);
    }

    /**
     * @param int $user
     * @param array $values
     * @return int
     * @throws Exception
     */
    public function create(int $user, array $values): int
    {
        $jsonValues = json_encode($values);

        $statement = $this->getEntityManager()
            ->getConnection()
            ->prepare('select create_customer(:user_id, :values)');
        $statement->bindValue('user_id', $user, \PDO::PARAM_INT);
        $statement->bindValue('values', $jsonValues, \PDO::PARAM_STR);

        return $statement->executeQuery()->fetchOne();
    }

}
