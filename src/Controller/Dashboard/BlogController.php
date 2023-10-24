<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DashboardNavbar;

#[Route('/dashboard/blog')]
class BlogController extends AbstractController
{

    use DashboardNavbar;

    const CHILDRENS = [
        'blog' => [
            'menu.dashboard.overview.blog' => 'app_dashboard_blog',
            'menu.dashboard.create.blog' => 'app_dashboard_create_blog',
            'menu.dashboard.latest.blog' => 'app_dashboard_latest_blog',
            'menu.dashboard.approved.blog' => 'app_dashboard_approved_blog',
        ],
    ];

    /**
     * 
     * @return array
     */
    public static function childrens(): array
    {
        return CHILDRENS;
    }

    #[Route('/', name: self::CHILDRENS['blog']['menu.dashboard.overview.blog'])]
    public function index(): Response
    {
        return $this->render('dashboard/content/blog/index.html.twig', $this->build() + [
                    'count' => 0,
                    'data' => 'Blog overview content',
        ]);
    }

    #[Route('/approved', name: self::CHILDRENS['blog']['menu.dashboard.approved.blog'])]
    public function approved(): Response
    {
        return $this->render('dashboard/content/blog/approved.html.twig', $this->build() + [
                    'data' => 'Blog approved content',
        ]);
    }

    #[Route('/latest', name: self::CHILDRENS['blog']['menu.dashboard.latest.blog'])]
    public function latest(): Response
    {
        return $this->render('dashboard/content/blog/latest.html.twig', $this->build() + [
                    'data' => 'Blog latest content',
        ]);
    }

    #[Route('/create', name: self::CHILDRENS['blog']['menu.dashboard.create.blog'])]
    public function create(): Response
    {
        return $this->render('dashboard/content/blog/create.html.twig', $this->build() + [
                    'data' => 'Blog create form',
        ]);
    }
}
