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
            ->select([
                'e.id',
                'e.slug',
                'e.created_at',
                'ed.title',
                'a.name as attach',
                'ud.first_name',
            ])
            ->leftJoin('e.entryCategories', 'ec', 'WITH', 'ec.entry = e.id')
            ->leftJoin('App\Entity\Category', 'c', 'WITH', 'c.id = ec.category')
            ->join('App\Entity\EntryDetails', 'ed', 'WITH', 'e.id = ed.entry')
            ->join('App\Entity\UserDetails', 'ud', 'WITH', 'e.user = ud.user')
            ->leftJoin('App\Entity\EntryAttachment', 'ea', 'WITH', 'ed.id = ea.details')
            ->leftJoin('App\Entity\Attach', 'a', 'WITH', 'a.id = ea.attach');

        if ($slug) {
            $qb->where('c.slug = :slug')->setParameter('slug', $slug);
        }

        $qb->andWhere('e.type = :type')
            ->setParameter('type', $type)
            ->andWhere('e.status = :status')
            ->setParameter('status', 'published')
            ->andWhere('e.deleted_at is null')
            ->groupBy('e.id');
        $qb->orderBy('e.id', 'desc')->setMaxResults($limit);

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getResult();
    }
}
