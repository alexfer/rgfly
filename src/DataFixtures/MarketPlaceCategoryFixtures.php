<?php

namespace App\DataFixtures;

use App\Entity\MarketPlace\MarketCategory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
class MarketPlaceCategoryFixtures extends Fixture
{

    private SluggerInterface $slugger;

    private const string REFERENCE_NAME = 'cat_%d';

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
        $this->loadProductCategories($manager);
    }

    /**
     *
     * @param ObjectManager $manager
     * @return void
     */
    private function loadProductCategories(ObjectManager $manager): void
    {
        foreach ($this->getProductCategoryData() as $key => [$name, $description, $parent]) {
            $category = new MarketCategory();
            $category->setName($name);
            $category->setSlug($this->slugger->slug($name)->lower());
            $category->setDescription($description);
            $category->setLevel(++$key);
            $category->setParent(null);
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
            ['Computers & Notebooks', 'Computers & Notebooks.', 0],
            ['Household Appliances', 'Household Appliances.', 0],
            ['Smartphones, TV and Electronics', 'Smartphones, TV and Electronics.', 0],
            ['Products for gamers', 'Products for gamers.', 0],
            ['Products for home', 'Products for home.', 0],
            ['Automotive Tools', 'Automotive Tools.', 0],
            ['Plumbing and repair', 'Plumbing and repair.', 0],
            ['Sport', 'Sport.', 7],
            ['Beauty and health', 'Beauty and health.', 0],
            ['Goods for Kids', 'Goods for Kids.', 0],
            ['Sales', 'Sales.', 0],
            ['Handmade', 'Handmade.', 0],
        ];
    }
}