<?php

namespace App\Service\MarketPlace\Market\Customer\Interface;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketOrders;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;

interface ProcessorInterface
{
    /**
     * @param MarketCustomer $customer
     * @param array $formData
     * @param MarketOrders $order
     * @return void
     */
    public function process(MarketCustomer $customer, array $formData, MarketOrders $order): void;

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
     * @param MarketCustomer $customer
     * @param mixed $formData
     * @return void
     */
    public function updateCustomer(MarketCustomer $customer, mixed $formData): void;

    /**
     * @param FormInterface $form
     * @return self
     */
    public function bind(FormInterface $form): self;
}