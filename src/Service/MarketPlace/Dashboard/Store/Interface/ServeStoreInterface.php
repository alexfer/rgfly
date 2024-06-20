<?php

namespace App\Service\MarketPlace\Dashboard\Store\Interface;

use App\Entity\MarketPlace\Store;
use Symfony\Component\Security\Core\User\UserInterface;

interface ServeStoreInterface
{
    /**
     * @param UserInterface $user
     * @return Store|null
     */
    public function supports(UserInterface $user): ?Store;

    /**
     * @return array
     */
    public function currency(): array;
}