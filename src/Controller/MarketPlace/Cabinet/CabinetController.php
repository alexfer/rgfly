<?php

namespace App\Controller\MarketPlace\Cabinet;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketCustomerOrders;
use App\Form\Type\MarketPlace\AddressType;
use App\Form\Type\MarketPlace\CustomerProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/cabinet')]
class CabinetController extends AbstractController
{

    private function customer(
        UserInterface          $user,
        EntityManagerInterface $em,
    ): MarketCustomer
    {
        return $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);
    }

    #[Route('', name: 'app_cabinet', methods: ['GET'])]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $this->customer($user, $em);

        $orders = $em->getRepository(MarketCustomerOrders::class)->findBy([
            'customer' => $customer,
        ], ['id' => 'desc']);

        return $this->render('market_place/cabinet/index.html.twig', [
            'customer' => $customer,
            'orders' => $orders,
        ]);
    }

    #[Route('/personal-information', name: 'app_cabinet_personal_information', methods: ['GET'])]
    public function personalInformation(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $this->customer($user, $em);
        $form = $this->createForm(CustomerProfileType::class, $customer);
        $form->handleRequest($request);

        return $this->render('market_place/cabinet/personal_information.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/wishlist', name: 'app_cabinet_wishlist', methods: ['GET'])]
    public function orders(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        return $this->render('market_place/cabinet/wishlist.html.twig', [
            'customer' => $this->customer($user, $em),
        ]);
    }

    #[Route('/address', name: 'app_cabinet_address', methods: ['GET'])]
    public function address(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $this->customer($user, $em);
        $address = $customer->getMarketAddress();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        return $this->render('market_place/cabinet/address.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

}