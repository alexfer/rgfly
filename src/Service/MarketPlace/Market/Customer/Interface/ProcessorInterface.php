<?php

namespace App\Service\MarketPlace\Market\Customer\Interface;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketOrders;
use App\Entity\User;

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
     * @param array $args
     * @return void
     */
    public function addCustomer(User $user, array $args): void;

    /**
     * @param array $args
     * @return void
     */
    public function updateCustomer(array $args): void;
}