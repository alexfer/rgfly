<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard')]
class IndexController extends AbstractController
{

    use Dashboard;

    /**
     * 
     * @return Response
     */
    #[Route('', name: 'app_dashboard')]
    public function index(UserInterface $user): Response
    {
        return $this->render('dashboard/content/index.html.twig', $this->build($user));
    }
}
