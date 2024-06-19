<?php

namespace App\Service\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Service\MarketPlace\Dashboard\Store\Serve;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

trait StoreTrait
{
    /**
     * @param Serve $serve
     * @param UserInterface $user
     * @return Store
     */
    protected function store(Serve $serve, UserInterface $user): Store
    {
        return $serve->handle($user);
    }

}