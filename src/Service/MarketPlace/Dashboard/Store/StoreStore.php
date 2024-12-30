<?php

namespace Inno\Service\MarketPlace\Dashboard\Store;

use Inno\Entity\MarketPlace\Store;
use Inno\Entity\User;
use Inno\Service\MarketPlace\Currency;
use Inno\Service\MarketPlace\Dashboard\Store\Interface\ServeStoreInterface;
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
        $criteria = ['id' => $this->request->get('store')];

        if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
            $criteria = ['id' => $this->request->get('store'), 'owner' => $user];
        }

        $store = $this->em->getRepository(Store::class)
            ->findOneBy($criteria);

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
        return $this->em->getRepository(Store::class)->extra($this->store);
    }
}