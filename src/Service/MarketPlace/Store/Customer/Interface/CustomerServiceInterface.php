<?php declare(strict_types=1);

namespace Inno\Service\MarketPlace\Store\Customer\Interface;

use Inno\Entity\MarketPlace\{StoreAddress, StoreCustomer, StoreOrders};
use Inno\Entity\User;
use Symfony\Component\Form\FormInterface;

interface CustomerServiceInterface
{
    /**
     * @param StoreCustomer $customer
     * @param array $formData
     * @param StoreOrders $order
     * @return void
     */
    public function process(StoreCustomer $customer, array $formData, StoreOrders $order): void;

    /**
     * @param string $password
     * @return User
     */
    public function addUser(string $password): User;

    /**
     * @param User $user
     * @return void
     */
    public function addCustomer(User $user): void;

    /**
     * @param StoreCustomer $customer
     * @param mixed $formData
     * @param bool $address
     * @return void
     */
    public function updateCustomer(StoreCustomer $customer, mixed $formData, bool $address = true): void;

    /**
     * @param FormInterface $form
     * @return self
     */
    public function bind(FormInterface $form): self;

    /**
     * @param StoreAddress $address
     * @param FormInterface $form
     * @return void
     */
    public function updateAddress(StoreAddress $address, FormInterface $form): void;
}