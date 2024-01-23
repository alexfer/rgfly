<?php

namespace App\DataFixtures\MarketPlace;

use App\Entity\MarketPlace\MarketCategory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{

    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    private const string REFERENCE_NAME = 'cat_%d';

    /**
     * @param SluggerInterface $slugger
     */
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

        foreach ($this->getProductCategoryData() as $key => $value) {
            $category = new MarketCategory();
            $category->setName($value['name']);
            $category->setSlug($this->slugger->slug($value['name'])->lower());
            $category->setDescription($value['description']);
            $category->setLevel(++$key);
            $category->setParent(null);
            $category->setCreatedAt(new DateTimeImmutable());
            $category->setDeletedAt(null);
            $manager->persist($category);

            if (count($value['parent'])) {

                foreach ($value['parent'] as $i => $children) {
                    $child = new MarketCategory();
                    $child->setParent($category);
                    $child->setName($children['name']);
                    $child->setSlug($this->slugger->slug($children['name'])->lower());
                    $child->setDescription($children['description']);
                    $child->setLevel(++$i);
                    $child->setCreatedAt(new DateTimeImmutable());
                    $child->setDeletedAt(null);
                    $manager->persist($child);
                }
            }
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
            [
                'name' => 'Computers & Notebooks',
                'description' => 'Computers & Notebooks.',
                'parent' => [
                    [
                        'name' => 'Computers, nettops, monoblocs',
                        'description' => 'Computers, nettops, monoblocs',
                    ],
                    [
                        'name' => 'Notebooks',
                        'description' => 'Notebooks',
                    ],
                    [
                        'name' => 'Monitors',
                        'description' => 'Monitors',
                    ],
                    [
                        'name' => 'Networks equipment',
                        'description' => 'Networks equipment',
                    ],
                    [
                        'name' => 'Keyboards and mouses',
                        'description' => 'Keyboards and mouses',
                    ],
                    [
                        'name' => 'Office equipment',
                        'description' => 'Office equipment',
                    ],
                ]],
            [
                'name' => 'Household Appliances',
                'description' => 'Household Appliances.',
                'parent' => [
                    [
                        'name' => 'Vacuum cleaners',
                        'description' => 'Vacuum cleaners',
                    ],
                    [
                        'name' => 'Washing machines',
                        'description' => 'Washing machines',
                    ],
                    [
                        'name' => 'Refrigeration & Food Safety',
                        'description' => 'Refrigeration & Food Safety',
                    ],
                    [
                        'name' => 'Microwave ovens',
                        'description' => 'Microwave ovens',
                    ],
                    [
                        'name' => 'Irons',
                        'description' => 'Irons',
                    ],
                ]],
            [
                'name' => 'Smartphones, TV and Electronics',
                'description' => 'Smartphones, TV and Electronics.',
                'parent' => [
                    [
                        'name' => 'TV & accessorise',
                        'description' => 'TV & accessorise',
                    ],
                    [
                        'name' => 'Cell phones & accessorises',
                        'description' => 'Cell phones & accessorises',
                    ],
                    [
                        'name' => 'Photo & Video',
                        'description' => 'Photo & Video',
                    ],
                    [
                        'name' => 'Audio and accessories',
                        'description' => 'Audio and accessories',
                    ],
                    [
                        'name' => 'Tablets',
                        'description' => 'Tablets',
                    ],
                    [
                        'name' => 'Hand watches',
                        'description' => 'Hand watches',
                    ],
                    [
                        'name' => 'Projection equipments',
                        'description' => 'Projection equipments',
                    ],
                ]],
            [
                'name' => 'Products for gamers',
                'description' => 'Products for gamers.',
                'parent' => [
                    [
                        'name' => 'Gaming gamepads',
                        'description' => 'Gaming gamepads',
                    ],
                    [
                        'name' => 'Game consoles',
                        'description' => 'Game consoles',
                    ],
                    [
                        'name' => 'Accessories for gamers',
                        'description' => 'Accessories for gamers',
                    ],
                    [
                        'name' => 'Games',
                        'description' => 'Games',
                    ],
                    [
                        'name' => 'Accessories and souvenirs',
                        'description' => 'Accessories and souvenirs',
                    ],
                ]],
            [
                'name' => 'Products for home',
                'description' => 'Products for home.',
                'parent' => [
                    [
                        'name' => 'Home textiles',
                        'description' => 'Home textiles',
                    ],
                    [
                        'name' => 'Dishes ware',
                        'description' => 'Dishes ware',
                    ],
                    [
                        'name' => 'Furniture\'s',
                        'description' => 'Furniture\'s',
                    ],
                    [
                        'name' => 'Kids room',
                        'description' => 'Kids room',
                    ],
                    [
                        'name' => 'Decor for home',
                        'description' => 'Decor for home',
                    ],
                    [
                        'name' => 'Clocks',
                        'description' => 'Clocks',
                    ],
                ]],
            [
                'name' => 'Automotive Tools',
                'description' => 'Automotive Tools.',
                'parent' => [

                ]],
            [
                'name' => 'Plumbing and repair',
                'description' => 'Plumbing and repair.',
                'parent' => [

                ]],
            [
                'name' => 'Sports & Leisure',
                'description' => 'Sports & Leisure.',
                'parent' => [
                    [
                        'name' => 'Sports equipment',
                        'description' => 'Sports equipment',
                    ],
                    [
                        'name' => 'Sports gear',
                        'description' => 'Sports gear',
                    ],
                    [
                        'name' => 'Hunting and fishing',
                        'description' => 'Hunting and fishing',
                    ],
                    [
                        'name' => 'Bicycles & motorcycles',
                        'description' => 'Bicycles & motorcycles',
                    ],
                    [
                        'name' => 'Sport simulators',
                        'description' => 'Sport simulators',
                    ],
                ]],
            [
                'name' => 'Beauty and health',
                'description' => 'Beauty and health.',
                'parent' => [

                ]],
            [
                'name' => 'Goods for Kids',
                'description' => 'Goods for Kids.',
                'parent' => [

                ]],
            [
                'name' => 'Sales',
                'description' => 'Sales.',
                'parent' => [

                ]],
            [
                'name' => 'Handmade',
                'description' => 'Handmade.',
                'parent' => [

                ]],
        ];
    }
}