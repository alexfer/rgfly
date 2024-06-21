<?php

namespace App\Service\MarketPlace\Dashboard\Store;

use App\Entity\MarketPlace\Store;
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\Dashboard\Store\Interface\ServeStoreInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class StoreStore extends Handle implements ServeStoreInterface
{

    protected Store $store;

    /**
     * @param UserInterface $user
     * @return StoreStore|null
     */
    public function supports(UserInterface $user): ?Store
    {
        $store = $this->em->getRepository(Store::class)
            ->findOneBy(['id' => $this->request->get('store'), 'owner' => $user]);

        if (!$store) {
            throw new AccessDeniedException();
        }
        $this->store = $store;
        return $this->store;
    }

    /**
     * @return array
     */
    public function currency(): array
    {
        return Currency::currency($this->store->getCurrency());
    }

    /**
     * @return array|null
     */
    public function extra(): ?array
    {
        return $this->em->getRepository(StoreStore::class)->extra($this->store);
    }
}