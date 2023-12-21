<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Repository\MarketPlace\MarketProductRepository;
use App\Repository\MarketPlace\MarketRepository;
use App\Service\Navbar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/market-place/product')]
class ProductController extends AbstractController
{
    use Navbar;

    #[Route('/', name: 'app_dashboard_market_place_product')]
    public function index(
        UserInterface           $user,
        MarketRepository        $marketRepository,
        MarketProductRepository $marketProductRepository,
    ): Response
    {
        $market = $marketRepository->findOneBy(['owner' => $user], ['id' => 'desc']);
        $entries = $marketProductRepository->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/product/index.html.twig', $this->build($user) + [
                'market' => $market,
                'entries' => $entries,
            ]);
    }
}
