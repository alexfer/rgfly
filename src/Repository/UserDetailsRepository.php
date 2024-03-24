<?php

namespace App\Repository;

use App\Entity\UserDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserDetails>
 *
 * @method UserDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDetails[]    findAll()
 * @method UserDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDetailsRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDetails::class);
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
            ->prepare('select create_user_details(:user_id, :values)');
        $statement->bindValue('user_id', $user, \PDO::PARAM_INT);
        $statement->bindValue('values', $jsonValues, \PDO::PARAM_STR);

        return $statement->executeQuery()->fetchOne();
    }
}
