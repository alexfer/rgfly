<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\Market;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request, Response,
};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/market')]
class MarketController extends AbstractController
{
    #[Route('', name: 'app_market_place_markets')]
    public function index(): Response
    {
        return $this->render('market_place/market/index.html.twig', []);
    }

    #[Route('/{slug}', name: 'app_market_place_market')]
    public function market(Request $request, Market $market): Response
    {
        return $this->render('market_place/market/market.html.twig', [
            'market' => $market,
        ]);
    }
}
