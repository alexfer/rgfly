<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\{Store, StoreCustomer};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place/store')]
class StoreController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('', name: 'app_market_place_stores')]
    public function index(): Response
    {
        return $this->render('market_place/store/index.html.twig', []);
    }

    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @param Store $store
     * @return Response
     */
    #[Route('/{slug}', name: 'app_market_place_market')]
    public function market(
        Request                $request,
        ?UserInterface         $user,
        EntityManagerInterface $em,
        Store                  $store,
    ): Response
    {
        $customer = $em->getRepository(StoreCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/store/index.html.twig', [
            'store' => $store,
            'customer' => $customer,
        ]);
    }

}
