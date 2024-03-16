<?php

namespace App\Service\MarketPlace\Market\Customer\Interface;

use App\Entity\MarketPlace\MarketCustomer;
use Symfony\Component\Security\Core\User\UserInterface;

interface MarketUserManagerInterface
{
    /**
     * @param UserInterface|null $user
     * @return MarketCustomer|null
     */
    public function getUserCustomer(?UserInterface $user): ?MarketCustomer;
}