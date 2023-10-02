<?php

namespace App\Controller\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/user')]
class UserController extends AbstractController
{

    #[Route('/', name: 'app_dashboard_user')]
    public function index(): Response
    {
        return $this->render('dashboard/content/user/index.html.twig', []);
    }
}
