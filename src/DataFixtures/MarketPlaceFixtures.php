<?php

namespace App\DataFixtures;

use App\Entity\MarketPlace\MarketPaymentGateway;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MarketPlaceFixtures extends Fixture
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
            MarketPlaceCategoryFixtures::class,
        ];
    }

    private function loadPaymentMethods(ObjectManager $manager): void
    {
        foreach ($this->getPaymentGatewayData() as $key => [$name, $summary]) {
            $gateway = new MarketPaymentGateway();
            $gateway->setName($name);
            $gateway->setSummary($summary);
            $manager->persist($gateway);

        }
        $manager->flush();
    }

    private function getPaymentGatewayData(): array
    {
        return [
            ['Stripe', 'Stripe is powerful payment platform designed for internet businesses, with the company claiming to handle billions of dollars worth of transactions'],
            ['Adyen', 'Adyen is built for both point-of-sale and online purchases, accepting a broad range of payments from major credit cards and providers like Apple Pay'],
            ['PayPal', 'With both services, PayPal adds fraud protection security without an additional charge — providing extra assurance that your payment gateway is safe and capable'],
            ['Apple Pay', 'Apple Pay is a safe, secure, and private way to pay. And Apple Pay Later gives you the flexibility to pay over time for purchases on iPhone or iPad'],
            ['Square', 'Ability to use Square to build a website from scratch, or use Square Checkout as the payment page for your existing site'],
        ];
    }
}