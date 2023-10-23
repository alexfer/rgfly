<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DashboardNavbar;

#[Route('/dashboard/news')]
class NewsController extends AbstractController
{
    const CHILDRENS = [
        'news' => [
            'menu.dashboard.oveview_news' => 'app_dashboard_news',
        ],
    ];
    
    #[Route('/', name: self::CHILDRENS['news']['menu.dashboard.oveview_news'])]
    public function index(): Response
    {
        return $this->render('dashboard/content/news/index.html.twig',  DashboardNavbar::build() + [
            'data' => 'Entry list of news type',
        ]);
    }
}
