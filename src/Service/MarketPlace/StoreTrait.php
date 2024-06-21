<?php

namespace App\Service\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Service\MarketPlace\Dashboard\Store\StoreStore;
use Symfony\Component\Security\Core\User\UserInterface;

trait StoreTrait
{
    /**
     * @param StoreStore $serve
     * @param UserInterface $user
     * @return Store
     */
    protected function store(StoreStore $serve, UserInterface $user): Store
    {
        return $serve->supports($user);
    }

}