<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param PasswordAuthenticatedUserInterface $user
     * @param string $newHashedPassword
     * @return void
     * @throws UnsupportedUserException
     */
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        string                             $newHashedPassword,
    ): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $email
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $email): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery('SELECT u FROM App\Entity\User u WHERE u.email = :query AND u.deleted_at IS NULL')
            ->setParameter('query', $email)
            ->getOneOrNullResult();
    }

    /**
     * @param array $values
     * @return int
     * @throws Exception
     */
    public function create(array $values): int
    {
        $jsonValues = json_encode($values);
        $statement = $this->getEntityManager()
            ->getConnection()
            ->prepare('select create_user(:values)');
        $statement->bindValue('values', $jsonValues, \PDO::PARAM_STR);

        return $statement->executeQuery()->fetchOne();
    }


    public function fetch(string $query = null, int $limit = 25, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select([
                'u.id',
                'u.created_at',
                'details.updated_at',
                'u.roles',
                'details.first_name',
                'details.last_name',
            ])
            ->join(UserDetails::class, 'details', 'WITH', 'u.id = details.user')
            ->where('details.first_name LIKE :query')
            ->orWhere('details.last_name LIKE :query')
            ->orWhere('u.email LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
