<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketOrders;
use App\Form\Type\Dashboard\MarketPlace\CustomerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/checkout')]
class CheckoutController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/{order}', name: 'app_market_place_order_checkout', methods: ['GET'])]
    public function checkout(
        Request                $request,
        EntityManagerInterface $em
    ): Response
    {
        $session = $request->getSession();

        $order = $em->getRepository(MarketOrders::class)->findOneBy([
            'number' => $request->get('order'),
            'session' => $session->getId(),
            'status' => MarketOrders::STATUS['processing'],
        ]);

        $customer = new MarketCustomer();

        $form = $this->createForm(CustomerType::class, $customer);

        if(!$order) {
            $this->redirectToRoute('app_market_place_order_summary');
        }

        return $this->render('market_place/checkout/index.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

}