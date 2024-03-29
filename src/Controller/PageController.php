<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{

    /**
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/terms', name: 'terms')]
    #[Route('/policy', name: 'policy')]
    #[Route('/about', name: 'about')]
    #[Route('/service', name: 'service')]
    public function index(Request $request): Response
    {
        $template = $request->attributes->get('_route');

        return $this->render('pages/' . $template . '.html.twig', [
            'data' => [],
        ]);
    }
}
