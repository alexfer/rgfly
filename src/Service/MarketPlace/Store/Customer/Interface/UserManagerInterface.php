<?php

namespace App\Service\MarketPlace\Store\Customer\Interface;

use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserManagerInterface
{
    /**
     * @param UserInterface|null $user
     * @return StoreCustomer|null
     */
    public function get(?UserInterface $user): ?StoreCustomer;

    /**
     * @param string $email
     * @return User|null
     */
    public function exists(string $email): ?User;
}