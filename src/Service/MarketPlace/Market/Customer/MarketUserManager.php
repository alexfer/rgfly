<?php

namespace App\Service\MarketPlace\Market\Customer;

use App\Entity\MarketPlace\MarketCustomer;
use App\Service\MarketPlace\Market\Customer\Interface\MarketUserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MarketUserManager implements MarketUserManagerInterface
{

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private readonly EntityManagerInterface $em)
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
}