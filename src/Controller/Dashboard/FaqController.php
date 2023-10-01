<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/faq')]
class FaqController extends AbstractController
{

    #[Route('/', name: 'app_dashboard_faq')]
    public function index(): Response
    {
        return $this->render('dashboard/content/faq/index.html.twig', [
                    'data' => 'Questions content',
        ]);
    }
    
    #[Route('/create', name: 'app_dashboard_faq_create')]
    public function create(): Response
    {
        return $this->render('dashboard/content/faq/index.html.twig', [
                    'data' => 'Questions content',
        ]);
    }
}
