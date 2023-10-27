<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Dashboard;
use App\Repository\EntryRepository;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/news')]
class NewsController extends AbstractController
{

    use Dashboard;

    const CHILDRENS = [
        'news' => [
            'menu.dashboard.oveview_news' => 'app_dashboard_news',
        ],
    ];

    #[Route('', name: self::CHILDRENS['news']['menu.dashboard.oveview_news'])]
    public function index(
            EntryRepository $reposiroty,
            UserInterface $user,
    ): Response
    {
        return $this->render('dashboard/content/blog/index.html.twig', $this->build($user) + [
                    'entries' => $reposiroty->findBy($this->criteria($user, ['type' => 'news']), ['id' => 'desc']),
        ]);
    }
}
