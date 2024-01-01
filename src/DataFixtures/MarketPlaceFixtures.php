<?php

namespace App\DataFixtures;

use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketPaymentGateway;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class MarketPlaceFixtures extends Fixture
{
    private SluggerInterface $slugger;

    private const string REFERENCE_NAME = 'market_%d';

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $this->loadProductCategories($manager);
    }

    /**
     *
     * @param ObjectManager $manager
     * @return void
     */
    private function loadProductCategories(ObjectManager $manager): void
    {
        foreach ($this->getProductCategoryData() as [$name, $description, $position]) {
            $category = new MarketCategory();
            $category->setName($name);
            $category->setSlug($this->slugger->slug($name)->lower());
            $category->setDescription($description);
            $category->setPosition($position);
            $category->setCreatedAt(new DateTimeImmutable());
            $category->setDeletedAt(null);
            $manager->persist($category);
        }
        $manager->flush();
    }

    private function loadPaymentMethods(ObjectManager $manager): void
    {
        foreach ($this->getPaymentGatewayData() as $key =>[$name, $summary]) {
            $gateway = new MarketPaymentGateway();
            $gateway->setName($name);
            $gateway->setSummary($summary);
            $manager->persist($gateway);

        }
        $manager->flush();
    }

    /**
     *
     * @return array
     */
    private function getProductCategoryData(): array
    {
        return [
            ['Computers & Notebooks', 'Computers & Notebooks.', 1],
            ['Smartphones, TV and Electronics', 'Smartphones, TV and Electronics.', 2],
            ['Products for gamers', 'Products for gamers.', 3],
            ['Products for home', 'Products for home.', 4],
            ['Automotive Tools', 'Automotive Tools.', 5],
            ['Plumbing and repair', 'Plumbing and repair.', 6],
            ['Sport', 'Sport.', 7],
            ['Beauty and health', 'Beauty and health.', 8],
            ['Good for Kids', 'Good for Kids.', 9],
            ['Sales', 'Sales.', 10],
            ['Handmade', 'Handmade.', 10],
        ];
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