<?php

namespace App\Repository;

use App\Entity\Entry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entry>
 *
 * @method Entry|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entry|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entry[]    findAll()
 * @method Entry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryRepository extends ServiceEntityRepository
{

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    /**
     * @param string|null $slug
     * @param string|null $type
     * @param int $limit
     * @return Entry[]|null
     */
    public function findEntriesByCondition(string $slug = null, string $type = null, int $limit = 12): ?array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.entryCategories', 'ec', 'WITH', 'ec.entry = e.id')
            ->leftJoin('App\Entity\Category', 'c', 'WITH', 'c.id = ec.category');

        if ($slug) {
            $qb->where('c.slug = :slug')->setParameter('slug', $slug);
        } else {
            return $this->findBy(['type' => $type], ['id' => 'desc'], $limit);
        }

        if ($limit) {
            $qb->orderBy('e.id',  'desc')->setMaxResults($limit);
        }
        return $qb->setCacheable(true)->getQuery()->getResult();
    }
}
