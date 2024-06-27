<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\{StoreCustomer, StoreProduct};
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place')]
class IndexController extends AbstractController
{
    /**
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_market_place_index')]
    public function index(
        ?UserInterface         $user,
        EntityManagerInterface $em,
    ): Response
    {
        $products = $em->getRepository(StoreProduct::class)->randomProducts(9);

        $customer = $em->getRepository(StoreCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/index.html.twig', [
            'products' => $products['data'],
            'customer' => $customer,
        ]);
    }

}
