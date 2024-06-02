<?php

namespace App\Controller\MarketPlace;

use App\Service\MarketPlace\Store\Customer\Interface\UserManagerInterface;
use App\Service\MarketPlace\Store\Order\Interface\{CollectionInterface,
    ComputeInterface,
    ProcessorInterface,
    ProductInterface,
    SummaryInterface};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place/order')]
class OrderController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param UserManagerInterface $userManager
     * @param SummaryInterface $orderSummary
     * @param CollectionInterface $orderCollection
     * @param ProductInterface $orderProduct
     * @return Response
     */
    #[Route('/summary/remove', name: 'app_market_place_order_remove_product', methods: ['POST'])]
    public function remove(
        Request              $request,
        ?UserInterface       $user,
        UserManagerInterface $userManager,
        SummaryInterface     $orderSummary,
        CollectionInterface  $orderCollection,
        ProductInterface     $orderProduct,
    ): Response
    {
        $customer = $userManager->getUserCustomer($user);
        $orderProduct->process($customer);

        $session = $request->getSession();
        $orders = $orderCollection->getOrders($session->getId());

        $countProducts = 0;
        $order = $orderProduct->getOrder();

        if ($order) {
            $products = $order->getStoreOrdersProducts();
            $countProducts = count($products);
        }
        $orderProducts = $orderCollection->getOrderProducts($session->getId());
        $session->set('quantity', $orderProducts);

        return $this->json([
            'products' => $countProducts,
            'summary' => $orderSummary->summary($orders, true),
            'removed' => $orderProduct->getStore()->getId(),
            'order' => count($orders) == 0,
            'quantity' => $session->get('quantity'),
            'redirect' => $this->generateUrl('app_market_place_order_summary'),
        ]);
    }


    /**
     * @param Request $request
     * @param ComputeInterface $compute
     * @return Response
     */
    #[Route('/summary', name: 'app_market_place_order_update', methods: ['POST'])]
    public function update(
        Request          $request,
        ComputeInterface $compute,
    ): Response
    {
        $input = $request->request->all();
        $compute->process($input);

        return $this->redirectToRoute('app_market_place_order_summary');
    }

    /**
     * @param Request $request
     * @param SummaryInterface $orderSummary
     * @param CollectionInterface $orderCollection
     * @return Response
     */
    #[Route('/summary', name: 'app_market_place_order_summary', methods: ['GET'])]
    public function summary(
        Request             $request,
        SummaryInterface    $orderSummary,
        CollectionInterface $orderCollection,
    ): Response
    {
        $session = $request->getSession();
        $orders = $orderCollection->getOrders($session->getId());

        return $this->render('market_place/order/summary.html.twig', [
            'orders' => $orders,
            'summary' => $orderSummary->summary($orders),
        ]);
    }

    /**
     * @param Request $request
     * @param CollectionInterface $orderCollection
     * @return JsonResponse
     */
    #[Route('/cart', name: 'app_market_place_product_order_cart', methods: ['POST', 'GET'])]
    public function cart(
        Request             $request,
        CollectionInterface $orderCollection,
    ): JsonResponse
    {
        $session = $request->getSession();
        $collection = $orderCollection->collection($session->getId());

        return $this->json([
            'template' => $this->renderView('market_place/cart.html.twig', ['orders' => $collection]),
            'quantity' => $session->get('quantity') ?: 0,
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param ProcessorInterface $orderProcessor
     * @param UserManagerInterface $userManager
     * @param CollectionInterface $orderCollection
     * @return JsonResponse
     */
    #[Route('/{product}', name: 'app_market_place_product_order', methods: ['POST'])]
    public function order(
        Request              $request,
        ?UserInterface       $user,
        ProcessorInterface   $orderProcessor,
        UserManagerInterface $userManager,
        CollectionInterface  $orderCollection,
    ): JsonResponse
    {
        $data = $request->toArray();

        if (!$data) {
            return $this->json([
                'error' => 'Invalid parameters provided.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $session = $request->getSession();

        if (!$session->isStarted()) {
            $session->start();
        }

        $customer = $userManager->getUserCustomer($user);
        $order = $orderProcessor->findOrder($session->getId());
        $orderProcessor->processOrder($order, $customer);
        $products = $orderCollection->getOrderProducts($session->getId());
        $session->set('quantity', $products);

        return $this->json([
            'quantity' => $session->get('quantity') ?: 1,
        ]);
    }
}