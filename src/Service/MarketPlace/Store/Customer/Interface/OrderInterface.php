<?php

namespace App\Service\MarketPlace\Store\Customer\Interface;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface OrderInterface
{
    /**
     * @param SessionInterface $session
     * @param UserInterface|null $user
     * @return void
     */
    public function apply(SessionInterface $session, ?UserInterface $user): void;
}