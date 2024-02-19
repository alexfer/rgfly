<?php

namespace App\DataFixtures\MarketPlace;

use App\Entity\MarketPlace\MarketPaymentGateway;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Fixtures extends Fixture
{

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadPaymentMethods($manager);
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function loadPaymentMethods(ObjectManager $manager): void
    {
        foreach ($this->getPaymentGatewayData() as $key => [$name, $summary, $active]) {
            $gateway = new MarketPaymentGateway();
            $gateway->setName($name);
            $gateway->setSummary($summary);
            $gateway->setActive($active);
            $manager->persist($gateway);

        }
        $manager->flush();
    }

    /**
     * @return array[]
     */
    private function getPaymentGatewayData(): array
    {
        return [
            ['Stripe', 'Stripe is powerful payment platform designed for internet businesses, with the company claiming to handle billions of dollars worth of transactions', false],
            ['Adyen', 'Adyen is built for both point-of-sale and online purchases, accepting a broad range of payments from major credit cards and providers like Apple Pay', false],
            ['PayPal', 'With both services, PayPal adds fraud protection security without an additional charge — providing extra assurance that your payment gateway is safe and capable', true],
            ['Apple Pay', 'Apple Pay is a safe, secure, and private way to pay. And Apple Pay Later gives you the flexibility to pay over time for purchases on iPhone or iPad', false],
            ['Square', 'Ability to use Square to build a website from scratch, or use Square Checkout as the payment page for your existing site', false],
            ['Cash on delivery', 'Pay with cash when your order is delivered', true],
        ];
    }
}