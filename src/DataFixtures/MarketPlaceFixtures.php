<?php

namespace App\DataFixtures;

use App\Entity\MarketPlace\MarketCategory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class MarketPlaceFixtures extends Fixture
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
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $this->loadroviders($manager);
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
}