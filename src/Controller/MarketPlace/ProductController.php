<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place/product')]
class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'app_market_place_product')]
    public function index(
        Request                $request,
        MarketProduct          $product,
        ?UserInterface         $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/product/index.html.twig', [
            'product' => $product,
            'customer' => $customer,
        ]);
    }
}
