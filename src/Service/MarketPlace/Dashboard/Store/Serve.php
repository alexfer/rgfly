<?php

namespace App\Service\MarketPlace\Dashboard\Store;

use App\Entity\MarketPlace\Store;
use App\Service\MarketPlace\Dashboard\Store\Interface\ServeInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class Serve extends Handle implements ServeInterface
{

    /**
     * @param UserInterface $user
     * @return Store|null
     */
    public function handle(UserInterface $user): ?Store
    {
        $store = $this->em->getRepository(Store::class)
            ->findOneBy(['id' => $this->request->get('store'), 'owner' => $user]);

        if (!$store) {
            throw new AccessDeniedException();
        }
        return $store;
    }
}