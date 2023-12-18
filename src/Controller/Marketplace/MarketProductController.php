<?php

namespace App\Controller\Marketplace;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarketProductController extends AbstractController
{

    #[Route('/marketplace/market/product', name: 'app_marketplace_market_product')]
    public function index(): Response
    {
        return $this->render('marketplace/product/index.html.twig', [
            'controller_name' => 'MarketProductController',
        ]);
    }
}
