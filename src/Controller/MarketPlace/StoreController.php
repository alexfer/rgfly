<?php

declare(strict_types=1);

namespace App\Controller\MarketPlace;

use App\Controller\Trait\ControllerTrait;
use App\Entity\MarketPlace\Store;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/store')]
class StoreController extends AbstractController
{
    use ControllerTrait;


    /**
     * @return Response
     */
    #[Route('', name: 'app_market_place_stores')]
    public function index(): Response
    {
        $random = $this->em->getRepository(Store::class)->random();

        return $this->render('market_place/store/index.html.twig', [
            'store' => $random['store'],
            'products' => $random['products'],
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/{slug}', name: 'app_market_place_market')]
    public function market(Request $request): Response
    {
        $customer = $this->getCustomer($this->getUser());

        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);
        $store = $this->em->getRepository(Store::class)->fetch($request->get('slug'), $customer, $offset, $limit);

        if ($store['result'] === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('market_place/store/store.html.twig', [
            'store' => $store['result'],
            'customer' => $customer,
        ]);
    }

}
