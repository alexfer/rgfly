<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place')]
class IndexController extends AbstractController
{
    #[Route('', name: 'app_market_place_index')]
    public function index(
        Request                $request,
        EntityManagerInterface $em,
    ): Response
    {
        $products = $em->getRepository(MarketProduct::class)->findBy(['deleted_at' => null], null, 8);
        shuffle($products);

        return $this->render('market_place/index.html.twig', [
            'products' => $products,
        ]);
    }
}
