<?php

namespace App\Service\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Service\MarketPlace\Dashboard\Store\ServeStoreStore;
use Symfony\Component\Security\Core\User\UserInterface;

trait StoreTrait
{
    /**
     * @param ServeStoreStore $serve
     * @param UserInterface $user
     * @return Store
     */
    protected function store(ServeStoreStore $serve, UserInterface $user): Store
    {
        return $serve->supports($user);
    }

}