<?php

namespace App\DataFixtures;

use App\Entity\{Category, User, UserDetails,};
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {

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
        foreach ($this->getCategoryData() as [$name, $order]) {
            $category = new Category();
            $category->setName($name);
            $category->setOrder($order);
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
            ['Main', 1],
            ['Back-end development'],
            ['Databases', 3],
            ['Architecture', 4],
            ['UX and UI design', 5],
            ['Business analysis', 6],
            ['Front-end development', 7],
            ['Testing and QA', 8],
            ['Integration', 9],
            ['Help desk', 10],
        ];
    }
}