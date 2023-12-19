<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Repository\MarketPlace\MarketRepository;
use App\Service\Navbar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/market-place')]
class MarketController extends AbstractController
{
    use Navbar;

    #[Route('/market', name: 'app_dashboard_market_place_market')]
    public function index(
        UserInterface   $user,
        MarketRepository $marketRepository,
    ): Response
    {

        $entries = $marketRepository->findBy(['owner' => $user], ['created_at' => 'desc']);

        return $this->render('dashboard/content/market_place/market/index.html.twig', $this->build($user) + [
            'entries' => $entries,
        ]);
    }
}
