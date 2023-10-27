<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Dashboard;
use App\Repository\EntryRepository;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/blog')]
class BlogController extends AbstractController
{

    use Dashboard;

    const CHILDRENS = [
        'blog' => [
            'menu.dashboard.overview.blogs' => 'app_dashboard_blog',
            'menu.dashboard.create.blog' => 'app_dashboard_create_blog',
        ],
    ];

    #[Route('', name: self::CHILDRENS['blog']['menu.dashboard.overview.blogs'])]
    public function index(
            EntryRepository $reposiroty,
            UserInterface $user,
    ): Response
    {
        return $this->render('dashboard/content/blog/index.html.twig', $this->build($user) + [
                    'entries' => $reposiroty->findBy($this->criteria($user, ['type' => 'blog']), ['id' => 'desc']),
        ]);
    }

    #[Route('/create', name: self::CHILDRENS['blog']['menu.dashboard.create.blog'])]
    public function create(UserInterface $user): Response
    {
        return $this->render('dashboard/content/blog/create.html.twig', $this->build($user) + [
                    'data' => 'Blog create form',
        ]);
    }
}
