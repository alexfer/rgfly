<?php

namespace App\DataFixtures;

use App\Entity\{Category, User, UserDetails,};
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $slugger;

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param SluggerInterface $slugger
     */
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        SluggerInterface                             $slugger,
    )
    {
        $this->slugger = $slugger;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadCategories($manager);
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function loadUsers(ObjectManager $manager): void
    {
        $userDetails = $this->getUserDetailsData();

        $key = 0;
        foreach ($this->getUserData() as [$password, $email, $roles, $ip]) {
            $user = new User();
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);
            $user->setIp($ip);
            $user->setCreatedAt(new DateTime());
            $user->setDeletedAt(null);

            $manager->persist($user);

            $details = new UserDetails();
            $details->setUser($user)
                ->setFirstName($userDetails[$key]['first_name'])
                ->setLastName($userDetails[$key]['last_name']);

            $manager->persist($details);
            $key++;
        }
        $manager->flush();
    }

    private function loadCategories(ObjectManager $manager): void
    {
        foreach ($this->getCategoryData() as [$name, $description, $position]) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug($this->slugger->slug($name)->lower());
            $category->setDescription($description);
            $category->setPosition($position);
            $category->setCreatedAt(new DateTime());
            $category->setDeletedAt(null);
            $manager->persist($category);
        }
        $manager->flush();
    }

    /**
     * @return array[]
     */
    private function getUserData(): array
    {
        return [
            ['7212104', 'alexandershtyher@gmail.com', [User::ROLE_ADMIN], '0.0.0.0'],
            ['7212104', 'autoportal@email.ua', [User::ROLE_USER], '0.0.0.0'],
            ['7212104', 'alexfer@online.ua', [User::ROLE_USER], '0.0.0.0'],
            ['joanna', 'joanna@example.com', [User::ROLE_USER], '0.0.0.0'],
            ['bobby', 'bobby@example.com', [User::ROLE_USER], '0.0.0.0'],
            ['UserTest', 'usertest@example.com', [User::ROLE_USER], '0.0.0.0'],
        ];
    }

    /**
     * @return array[]
     */
    private function getUserDetailsData(): array
    {
        return [
            ['first_name' => 'Alexander', 'last_name' => 'Sh'],
            ['first_name' => 'Auto portal', 'last_name' => 'Last name'],
            ['first_name' => 'Олександр', 'last_name' => 'Штихер'],
            ['first_name' => 'Joanna', 'last_name' => 'Smith'],
            ['first_name' => 'Bobby', 'last_name' => 'Smith'],
            ['first_name' => 'User', 'last_name' => 'Test'],
        ];
    }

    /**
     * @return array[]
     */
    private function getCategoryData(): array
    {
        return [
            ['Main', 'The main topics is the most important one of several or more similar things.', 1],
            ['Back-end development', 'Every program that\'s written can usually be broadly categorized into two parts the back end code and the front end.', 2],
            ['Databases', 'Category of software that comprises word processing of databases etc.', 3],
            ['Architecture', 'Architecture is the art and science of designing buildings.', 4],
            ['UX and UI design', 'What is the difference between web design and UI UX design.', 5],
            ['Business analysis', 'Business analysis is a professional discipline focused on identifying business needs and determining solutions to business problems.', 6],
            ['Front-end development', 'Every program that\'s written can usually be broadly categorized into two parts the front end code and the back end.', 7],
            ['Testing and QA', 'Types of QA testing: everything you need to know.', 8],
            ['Integration', 'Want to know more about integration services?', 9],
            ['Help desk', 'The most common and generally best way to organize your support tickets is by issue type.', 10],
        ];
    }
}