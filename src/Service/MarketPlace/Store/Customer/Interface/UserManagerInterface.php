<?php

namespace Essence\Service\MarketPlace\Store\Customer\Interface;

use Essence\Entity\MarketPlace\StoreCustomer;
use Essence\Entity\User;
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