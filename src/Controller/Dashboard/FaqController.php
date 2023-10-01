<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FaqController extends AbstractController
{
    #[Route('/dashboard/faq', name: 'app_dashboard_faq')]
    public function index(): Response
    {
        return $this->render('dashboard/content/faq/index.html.twig', [
            'data' => 'Questions content',
        ]);
    }
}
