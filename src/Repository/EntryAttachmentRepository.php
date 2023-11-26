<?php

namespace App\Repository;

use App\Entity\Entry;
use App\Entity\Attach;
use App\Entity\EntryAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * @extends ServiceEntityRepository<EntryAttachment>
 *
 * @method EntryAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntryAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntryAttachment[]    findAll()
 * @method EntryAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntryAttachment::class);
    }

    public function getEntityAttachments(
        Entry        $entry,
        CacheManager $cacheManager,
        string       $path,
        string       $filter = 'entry_preview',
    ): array
    {
        $result = $this->createQueryBuilder('ea')
            ->select('a.id', 'a.mime', 'a.name', 'a.size', 'a.created_at')
            ->leftJoin(Attach::class, 'a', 'WITH', 'ea.id = a.id')
            ->where('ea.details = :details')
            ->setParameter('details', $entry->getEntryDetails())
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        $attachments = [];
        $id = $entry->getEntryDetails()->getId();

        foreach ($result as $attachment) {
            $attachments[] = [
                'id' => $attachment['id'],
                'name' => $attachment['name'],
                'size' => $attachment['size'],
                'url' => $cacheManager->generateUrl("{$path}/{$id}/{$attachment['name']}", $filter),
                'created_at' => $attachment['created_at'],
            ];
        }

        return $attachments;
    }

}
