<?php

namespace App\Controller\Dashboard;

use App\Repository\EntryRepository;
use App\Service\Dashboard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/news')]
class NewsController extends AbstractController
{

    use Dashboard;

    const CHILDRENS = [
        'news' => [
            'menu.dashboard.overview_news' => 'app_dashboard_news',
        ],
    ];

    #[Route('', name: self::CHILDRENS['news']['menu.dashboard.overview_news'])]
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
