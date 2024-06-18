<?php

namespace App\Service\MarketPlace\Store\Customer;

use App\Entity\MarketPlace\{StoreCustomer, StoreCustomerOrders, StoreOrders};
use App\Service\MarketPlace\Store\Customer\Interface\OrderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Order implements OrderInterface
{

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private readonly EntityManagerInterface $em)
    {

    }

    /**
     * @param SessionInterface $session
     * @param UserInterface|null $user
     * @return void
     */
    public function apply(SessionInterface $session, ?UserInterface $user): void
    {
        $orders = $this->em->getRepository(StoreOrders::class)->findBy(['session' => $session->getId()]);

        foreach ($orders as $order) {
            $customerOrder = $this->em->getRepository(StoreCustomerOrders::class)
                ->findOneBy(['orders' => $order, 'customer' => null]);
            $customer = $this->em->getRepository(StoreCustomer::class)->findOneBy(['member' => $user]);
            $customerOrder->setCustomer($customer);
            $this->em->persist($customerOrder);
            $order->setSession(null);
            $this->em->persist($order);
        }
        $session->set('quantity', 0);
        $this->em->flush();
    }
}