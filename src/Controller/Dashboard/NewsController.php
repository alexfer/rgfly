<?php

namespace App\Controller\Dashboard;

use App\Repository\EntryRepository;
use App\Service\Navbar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/news')]
class NewsController extends AbstractController
{

    use Navbar;

    const CHILDREN = [
        'news' => [
            'menu.dashboard.overview_news' => 'app_dashboard_news',
        ],
    ];

    /**
     * @param EntryRepository $repository
     * @param UserInterface $user
     * @return Response
     */
    #[Route('', name: self::CHILDREN['news']['menu.dashboard.overview_news'])]
    public function index(
        EntryRepository $repository,
        UserInterface   $user,
    ): Response
    {
        $entries = $repository->findBy($this->criteria($user, ['type' => 'news']), ['id' => 'desc']);

        return $this->render('dashboard/content/news/index.html.twig', $this->build($user) + [
                'entries' => $entries,
            ]);
    }
}
