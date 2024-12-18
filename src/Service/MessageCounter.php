<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreMessage;
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
    public function dashboard(int $id): int
    {
        $user = $this->manager->getRepository(User::class)->find($id);
        $stores = $user->getStores()->toArray();
        return $this->manager->getRepository(StoreMessage::class)->countMessages($stores);
    }

    /**
     * @param int $id
     * @return int
     */
    public function cabinet(int $id): int
    {
        $user = $this->manager->getRepository(User::class)->find($id);
        return $this->manager->getRepository(StoreCustomer::class)->countMessages($user);
    }
}