<?php

namespace Inno\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{

    /**
     * @param Request $request
     * @param ParameterBagInterface $params
     * @return Response
     */
    #[Route('/terms', name: 'terms')]
    #[Route('/policy', name: 'policy')]
    #[Route('/cookie-policy', name: 'cookie')]
    #[Route('/about', name: 'about')]
    #[Route('/service', name: 'service')]
    public function index(
        Request               $request,
        ParameterBagInterface $params,
    ): Response
    {
        $template = $request->attributes->get('_route');

        return $this->render('pages/' . $template . '.html.twig', [
            'data' => [
                'email' => $params->get('app.notifications.email_sender'),
            ],
        ]);
    }
}
