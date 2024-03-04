<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request, Response,
};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place/market')]
class MarketController extends AbstractController
{
    #[Route('', name: 'app_market_place_markets')]
    public function index(): Response
    {
        return $this->render('market_place/market/index.html.twig', []);
    }

    #[Route('/{slug}', name: 'app_market_place_market')]
    public function market(
        Request $request,
        ?UserInterface          $user,
        EntityManagerInterface  $em,
        Market $market,
    ): Response
    {
        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/market/index.html.twig', [
            'market' => $market,
            'customer' => $customer,
        ]);
    }
}
