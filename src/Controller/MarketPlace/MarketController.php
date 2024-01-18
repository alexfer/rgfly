<?php

namespace App\Controller\MarketPlace;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/market')]
class MarketController extends AbstractController
{
    #[Route('', name: 'app_market_place_market')]
    public function index(): Response
    {
        return $this->render('market_place/market/index.html.twig', []);
    }
}
