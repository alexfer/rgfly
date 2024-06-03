<?php

namespace App\DataFixtures\MarketPlace;

use App\Entity\MarketPlace\StorePaymentGateway;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class Fixtures extends Fixture
{

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

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
        foreach ($this->getPaymentGatewayData() as [$name, $summary, $active, $icon, $handler_text]) {
            $gateway = new StorePaymentGateway();
            $gateway->setName($name);
            $gateway->setSlug($this->slugger->slug($name)->lower());
            $gateway->setIcon($icon);
            $gateway->setHandlerText($handler_text);
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
            ['Stripe', 'Stripe is powerful payment platform designed for internet businesses', false, 'fa fa-stripe', 'Checkout with Stripe'],
            ['PayPal', 'PatPal - the safer, easier way to pay', false, 'fa fa-paypal', 'Pay with Paypal'],
            ['ApplePay', 'Apple Pay is a safe, secure, and private way to pay', false, 'fa fa-apple', 'Checkout'],
            ['Cash', 'Pay with cash when your order is delivered', true, 'fa fa-money', 'Checkout'],
        ];
    }
}