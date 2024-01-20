<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/product')]
class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'app_market_place_product')]
    public function index(
        Request $request,
        MarketProduct $product,
    ): Response
    {
        return $this->render('market_place/product/index.html.twig', [
            'product' => $product,
        ]);
    }
}
