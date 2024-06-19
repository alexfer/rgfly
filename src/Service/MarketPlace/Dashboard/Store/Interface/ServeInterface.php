<?php

namespace App\Service\MarketPlace\Dashboard\Store\Interface;

use App\Entity\MarketPlace\Store;
use Symfony\Component\Security\Core\User\UserInterface;

interface ServeInterface
{
    /**
     * @param UserInterface $user
     * @return Store|null
     */
    public function handle(UserInterface $user): ?Store;
}