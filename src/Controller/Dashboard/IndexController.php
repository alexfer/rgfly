<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DashboardNavbar;

class IndexController extends AbstractController
{

    /**
     * 
     * @return Response
     */
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/content/index.html.twig', DashboardNavbar::build());
    }
}
