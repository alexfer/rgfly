<?php

namespace App\Controller\MarketPlace\Cabinet;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketCustomerOrders;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/cabinet')]
class CabinetController extends AbstractController
{
    #[Route('', name: 'app_cabinet', methods: ['GET'])]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        $orders = $em->getRepository(MarketCustomerOrders::class)->findBy([
            'customer' => $customer,
        ]);

        return $this->render('market_place/cabinet/index.html.twig', [
            'customer' => $customer,
            'orders' => $orders,
        ]);
    }

    #[Route('/personal-information', name: 'app_cabinet_personal_information', methods: ['GET'])]
    public function personalInformation(
        Request $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);
        return $this->render('market_place/cabinet/personal_information.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/wishlist', name: 'app_cabinet_wishlist', methods: ['GET'])]
    public function orders(
        Request $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);
        return $this->render('market_place/cabinet/wishlist.html.twig', [
            'customer' => $customer,
        ]);
    }
}