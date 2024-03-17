<?php

namespace App\Service\MarketPlace\Market\Customer;

use App\Entity\MarketPlace\MarketAddress;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketCustomerOrders;
use App\Entity\MarketPlace\MarketOrders;
use App\Entity\User;
use App\Service\MarketPlace\Market\Customer\Interface\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Processor implements ProcessorInterface
{

    /**
     * @var mixed
     */
    protected mixed $formData;

    protected array $args;

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
        protected RequestStack                       $requestStack,
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
     * @return void
     */
    public function addCustomer(User $user): void
    {
        $this->customer->setMember($user);
        $this->em->persist($this->customer);
        $this->postUpdateAddress(new MarketAddress(), null);
        $this->updateOrder();
    }

    /**
     * @param MarketAddress $address
     * @param MarketCustomer|null $customer
     * @return void
     */
    protected function postUpdateAddress(MarketAddress $address, ?MarketCustomer $customer): void
    {
        $address->setCustomer($customer ?: $this->customer)
            ->setLine1($this->args['line1'])
            ->setLine2($this->args['line2'])
            ->setCity($this->args['city'])
            ->setCountry($this->args['country'])
            ->setPostal($this->args['postal'])
            ->setPhone($this->args['phone'])
            ->setRegion($this->args['region']);
        if ($customer) {
            $address->setUpdatedAt(new \DateTime());
        }
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
     * @param mixed $formData
     * @return void
     */
    public function updateCustomer(MarketCustomer $customer, mixed $formData): void
    {
        $customer->setFirstName($formData->getFirstName())
            ->setLastName($formData->getLastName())
            ->setEmail($formData->getEmail())
            ->setCountry($formData->getCountry())
            ->setPhone($formData->getPhone())
            ->setUpdatedAt(new \DateTime());

        $this->em->persist($customer);
        $this->postUpdateAddress($customer->getMarketAddress(), $customer);
    }

    public function __destruct()
    {
        $this->em->flush();
    }

    /**
     * @param FormInterface $form
     * @return $this
     */
    public function bind(FormInterface $form): self
    {
        $this->args = [
            'line1' => $form->get('line1')->getData(),
            'line2' => $form->get('line2')->getData(),
            'city' => $form->get('city')->getData(),
            'region' => $form->get('region')->getData(),
            'postal' => $form->get('postal')->getData(),
            'country' => $form->get('country')->getData(),
            'phone' => $form->get('phone')->getData(),
        ];
        return $this;
    }
}