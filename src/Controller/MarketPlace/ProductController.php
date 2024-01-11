<?php

namespace App\Controller\MarketPlace;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/product')]
class ProductController extends AbstractController
{
    #[Route('', name: 'app_market_place_product')]
    public function index(): Response
    {
        return $this->render('market_place/product/index.html.twig', []);
    }
}
