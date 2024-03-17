<?php

namespace App\Service\MarketPlace\Market\Customer;

use App\Entity\MarketPlace\MarketAddress;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketCustomerOrders;
use App\Entity\MarketPlace\MarketOrders;
use App\Entity\User;
use App\Service\MarketPlace\Market\Customer\Interface\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Processor implements ProcessorInterface
{

    /**
     * @var mixed
     */
    protected mixed $formData;

    /**
     * @var MarketCustomer
     */
    protected MarketCustomer $customer;

    /**
     * @var MarketOrders
     */
    protected MarketOrders $order;

    /**
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(
        protected RequestStack                  $requestStack,
        private readonly EntityManagerInterface      $em,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    )
    {

    }

    /**
     * @param MarketCustomer $customer
     * @param mixed $formData
     * @param MarketOrders $order
     * @return void
     */
    public function process(MarketCustomer $customer, mixed $formData, MarketOrders $order): void
    {
        $this->formData = $formData;
        $this->customer = $customer;
        $this->order = $order;
    }

    /**
     * @param string $password
     * @return User
     */
    public function addUser(string $password): User
    {
        $user = new User();
        $user->setEmail($this->formData->getEmail())
            ->setPassword($this->userPasswordHasher->hashPassword($user, $password))
            ->setIp($this->requestStack->getCurrentRequest()->getClientIp())
            ->setRoles([User::ROLE_CUSTOMER]);
        $this->em->persist($user);
        return $user;
    }

    /**
     * @param User $user
     * @param array $args
     * @return void
     */
    public function addCustomer(User $user, array $args): void
    {
        $this->customer->setMember($user);
        $this->em->persist($this->customer);
        $this->postUpdateAddress(new MarketAddress(), $args);
        $this->updateOrder();
    }

    /**
     * @param MarketAddress $address
     * @param array $args
     * @return void
     */
    protected function postUpdateAddress(MarketAddress $address, array $args): void
    {
        $address->setCustomer($this->customer)
            ->setLine1($args['line1'])
            ->setLine2($args['line2'])
            ->setCity($args['city'])
            ->setCountry($args['country'])
            ->setPostal($args['postal'])
            ->setPhone($args['phone'])
            ->setRegion($args['region']);
        $this->em->persist($address);
    }

    /**
     * @return void
     */
    private function updateOrder(): void
    {
        $order = $this->em->getRepository(MarketCustomerOrders::class)->findOneBy(['orders' => $this->order]);
        $order->setCustomer($this->customer);
        $this->em->persist($order);

    }

    /**
     * @param array $args
     * @return void
     */
    public function updateCustomer(array $args): void
    {
        $this->customer->setFirstName($this->formData->getFirstName())
            ->setLastName($this->formData->getLastName())
            ->setEmail($this->formData->getEmail())
            ->setCountry($this->formData->getCountry())
            ->setPhone($this->formData->getPhone())
            ->setUpdatedAt(new \DateTime());

        $this->em->persist($this->customer);
        $this->postUpdateAddress($this->customer->getMarketAddress(), $args);
    }

    public function __destruct()
    {
        $this->em->flush();
    }
}