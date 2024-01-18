<?php

namespace App\Controller\Dashboard;

use App\Repository\EntryRepository;
use App\Service\Dashboard;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/news')]
class NewsController extends AbstractController
{

    use Dashboard;

    const array CHILDREN = [
        'news' => [
            'menu.dashboard.overview_news' => 'app_dashboard_news',
        ],
    ];

    /**
     * @param EntryRepository $repository
     * @param UserInterface $user
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('', name: self::CHILDREN['news']['menu.dashboard.overview_news'])]
    public function index(
        EntryRepository $repository,
        UserInterface   $user,
    ): Response
    {
        $entries = $repository->findBy($this->criteria($user, ['type' => 'news']), ['id' => 'desc']);

        return $this->render('dashboard/content/news/index.html.twig', $this->navbar() + [
                'entries' => $entries,
            ]);
    }
}
