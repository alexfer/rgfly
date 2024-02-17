<?php

namespace App\Controller\MarketPlace;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cabinet')]
class CabinetController extends AbstractController
{
    #[Route('', name: 'app_cabinet', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('market_place/cabinet/index.html.twig', []);
    }
}