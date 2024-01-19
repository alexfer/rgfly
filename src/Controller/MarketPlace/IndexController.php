<?php

namespace App\Controller\MarketPlace;

use App\Repository\MarketPlace\MarketProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place')]
class IndexController extends AbstractController
{
    #[Route('', name: 'app_market_place_index')]
    public function index(
        Request                 $request,
        MarketProductRepository $marketProductRepository,
    ): Response
    {
        // TODO: Replace with psql function - get_products
        $products = $marketProductRepository->getProducts(8);
        //dd($products);
        shuffle($products);
        return $this->render('market_place/index.html.twig', [
            'products' => $products,
        ]);
    }
}
