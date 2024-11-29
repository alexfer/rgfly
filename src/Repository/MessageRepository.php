<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param Conversation $conversation
     * @param bool $status
     * @return void
     */
    public function setRead(Conversation $conversation, bool $status = true): void
    {
        $this->createQueryBuilder('m')
            ->set('m.is_read', ':is_read')
            ->setParameter('is_read', $status)
            ->where('m.conversation = :conversation')
            ->setParameter('conversation', $conversation)
            ->update()
            ->getQuery()
            ->execute();
    }
}
