<?php

namespace App\Service\MarketPlace\Market\Customer;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\User;
use App\Service\MarketPlace\Market\Customer\Interface\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UserManager implements UserManagerInterface
{

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {

    }

    /**
     * @param UserInterface|null $user
     * @return MarketCustomer|null
     */
    public function getUserCustomer(?UserInterface $user): ?MarketCustomer
    {
        $customer = $this->em->getRepository(MarketCustomer::class)
            ->findOneBy(['member' => $user]);

        if (!$customer) {
            $customer = new MarketCustomer();
        }
        return $customer;
    }

    public function existsCustomer(string $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }
}