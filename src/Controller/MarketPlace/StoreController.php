<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\{Store, StoreCustomer};
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place/store')]
class StoreController extends AbstractController
{
    /**
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_market_place_stores')]
    public function index(EntityManagerInterface $em): Response
    {
        $random = $em->getRepository(Store::class)->random();

        return $this->render('market_place/store/index.html.twig', [
            'store' => $random['store'],
            'products' => $random['products'],
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('/{slug}', name: 'app_market_place_market')]
    public function market(
        Request                $request,
        ?UserInterface         $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $em->getRepository(StoreCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);
        $store = $em->getRepository(Store::class)->fetch($request->get('slug'), $customer, $offset, $limit);

        if ($store['result'] === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('market_place/store/store.html.twig', [
            'store' => $store['result'],
            'customer' => $customer,
        ]);
    }

}
