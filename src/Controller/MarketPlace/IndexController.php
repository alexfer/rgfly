<?php

declare(strict_types=1);

namespace App\Controller\MarketPlace;

use App\Controller\Trait\ControllerTrait;
use App\Entity\MarketPlace\StoreProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place')]
class IndexController extends AbstractController
{
    use ControllerTrait;

    /**
     * @return Response
     */
    #[Route('', name: 'app_market_place_index')]
    public function index(): Response
    {
        $products = $this->em->getRepository(StoreProduct::class)->randomProducts(9);

        $customer = $this->getCustomer($this->getUser());

        return $this->render('market_place/index.html.twig', [
            'products' => $products['data'],
            'customer' => $customer,
        ]);
    }

}
