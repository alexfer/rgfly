<?php

namespace App\Controller\MarketPlace;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/checkout')]
class CheckoutController extends AbstractController
{

    #[Route('', name: 'app_market_place_order_checkout', methods: ['GET'])]
    public function checkout(Request $request): Response
    {
        return $this->render('market_place/checkout/index.html.twig', ['order' => [

        ]]);
    }

}