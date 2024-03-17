<?php

namespace App\Service\MarketPlace\Market\Customer\Interface;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserManagerInterface
{
    /**
     * @param UserInterface|null $user
     * @return MarketCustomer|null
     */
    public function getUserCustomer(?UserInterface $user): ?MarketCustomer;

    /**
     * @param string $email
     * @return User|null
     */
    public function existsCustomer(string $email): ?User;
}