<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketCustomer;
use App\Repository\MarketPlace\MarketProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place')]
class IndexController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @param MarketProductRepository $marketProductRepository
     * @return Response
     */
    #[Route('', name: 'app_market_place_index')]
    public function index(
        Request                 $request,
        ?UserInterface          $user,
        EntityManagerInterface  $em,
        MarketProductRepository $marketProductRepository,
    ): Response
    {
        // TODO: Replace with psql function - get_products
        $products = $marketProductRepository->getProducts(8);
        //dd($products);
        shuffle($products);

        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/index.html.twig', [
            'products' => $products,
            'customer' => $customer,
        ]);
    }
}
