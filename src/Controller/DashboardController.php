<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_USER_USER')")]
class DashboardController extends AbstractController
{

    #[Route('/dashboard', name: 'app_dashboard')]    
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
                    'controller_name' => 'DashboardController',
        ]);
    }
}
