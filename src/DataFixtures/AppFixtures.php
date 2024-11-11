<?php

namespace App\DataFixtures;

use App\DataFixtures\MarketPlace\Fixtures;
use App\Entity\{Category, Faq, User, UserDetails, UserSocial};
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    private SluggerInterface $slugger;

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
        $this->loadQuestions($manager);
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            Fixtures::class,
        ];
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

            $social = new UserSocial();
            $social->setDetails($details);

            $manager->persist($social);

            $key++;
        }
        $manager->flush();
    }

    /**
     *
     * @param ObjectManager $manager
     * @return void
     */
    private function loadCategories(ObjectManager $manager): void
    {
        foreach ($this->getCategoryData() as [$name, $description, $position]) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug($this->slugger->slug($name)->lower()->toString());
            $category->setDescription($description);
            $category->setPosition($position);
            $category->setCreatedAt(new DateTime());
            $category->setDeletedAt(null);
            $manager->persist($category);
        }
        $manager->flush();
    }

    /**
     *
     * @param ObjectManager $manager
     * @return void
     */
    private function loadQuestions(ObjectManager $manager): void
    {
        foreach ($this->getQuestionsData() as [$title, $content]) {
            $faq = new Faq();
            $faq->setTitle($title)->setContent($content)->setVisible(1);
            $manager->persist($faq);
        }

        $manager->flush();
    }

    /**
     *
     * @return array
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
     *
     * @return array
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
     *
     * @return array
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

    /**
     *
     * @return array
     */
    private function getQuestionsData(): array
    {
        return [
            ['Where do your developers come from?', '<b>Techspace works</b> from Ukraine, Poland, Hungary, Bulgaria, Serbia, Romania, Croatia, Spain, Portugal and Macedonia.'],
            ['Do your developers have experience with Agile?', 'Yes. Our developers are trained in Agile, SCRUM and Kanban. Our specialists are trained and experienced in project-based working according to the Agile methodology, so they can also join your organization immediately.'],
            ['When is Nearshore right for my organization?', 'Nearshore is suitable for virtually all organizations. Through Corona, we have all adapted to working from home and remotely and have become accustomed to online meetings. As a result, scheduling work and sharing updates through online platforms has become increasingly normal and has removed the biggest barrier to nearshoring. So as long as you English speaks and has an idea of what you want your developer or team to develop, nearshore development is right for your organization.'],
            ['What happens if we are not satisfied with a developer?', 'If you are not satisfied with the performance of a developer, you initially report this to us. We then engage with him/her to resolve the problem. If it appears that the problem is not resolvable between you and the developer, we will within 2 to 4 weeks find a suitable replacement for you.'],
            ['Can you support me in managing my team?', 'We have 15 years of experience setting up collaborations and remote teams. We are happy to advise you and share best practices with you.'],
            ['What is Nearshore?', 'Nearshore is a technical term for supplementing or setting up a team, abroad but close to home. In contrast to Outsourcing or Outstaffing, Nearshore is really a collaborative model between customer and supplier, where our role is to find and retain the specialists you need. Where as the client is responsible for the direction and risks of the project. The emphasis is on collaboration as Nearshore team members work closely with your internal staff in order to deliver your projects quickly and with high quality.'],
            ['We need a large development team, can you help?', 'Of course, we would love to get in touch with you. Depending on your needs, we can indicate whether we can help you with your request or not. This will mainly depend on how many developers, the techniques and how quickly the team needs to start. Feel free to contact us at info@techspace.com.'],
            ['I am looking for only 1 developer, can you help me?', 'Of course, our services can be purchased per 1 FTE. For support services, such as DevOps– or QA as a service, a minimum of 16 hours per week applies. A specialist will do the maximum feasible with the hours you purchase. So by taking less hours, you also have less capacity or operational clout to achieve your goals. Do you find it difficult to determine how many hours of support you need for your projects? Then contact us without obligation at info@techspace.com.'],
            ['Can my team come to our headquarters on a business trip?', 'Yes. We can completely take care of this for you by arranging airfare, visas, and lodging for the period you have your team on your <b>head office</b> want to invite. Before we arrange everything, we will of course coordinate all costs with you. We do not charge a fee for this service, the costs incurred can be paid by you directly.'],
        ];
    }

}
