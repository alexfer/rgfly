<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/blog')]
class BlogController extends AbstractController
{

    #[Route('/', name: 'app_dashboard_blog')]
    public function index(): Response
    {
        return $this->render('dashboard/content/blog/index.html.twig', [
                    'data' => 'Blog overview content',
        ]);
    }

    #[Route('/approved', name: 'app_dashboard_blog_approved')]
    public function approved(): Response
    {
        return $this->render('dashboard/content/blog/approved.html.twig', [
            'data' => 'Blog approved content',
        ]);
    }

    #[Route('/latest', name: 'app_dashboard_blog_latest')]
    public function latest(): Response
    {
        return $this->render('dashboard/content/blog/latest.html.twig', [
            'data' => 'Blog latest content',
        ]);
    }

    #[Route('/create', name: 'app_dashboard_blog_create')]
    public function create(): Response
    {
        return $this->render('dashboard/content/blog/create.html.twig', [
            'data' => 'Blog create form',
        ]);
    }
}
