<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class MessageCounter
{
    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        private EntityManagerInterface $manager,
    )
    {

    }

    /**
     * @param int $id
     * @return int
     */
    public function total(int $id): int
    {
        $user = $this->manager->getRepository(User::class)->find($id);
        return $user->getParticipants()->count();
    }
}