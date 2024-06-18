<?php

namespace App\Controller\MarketPlace;

use App\Service\MarketPlace\Store\Customer\Interface\UserManagerInterface;
use App\Service\MarketPlace\Store\Order\Interface\{CollectionInterface,
    ComputeInterface,
    ProcessorInterface,
    ProductInterface,
    SummaryInterface};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
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
        $customer = $userManager->get($user);
        $orderProduct->process($customer);

        $session = $request->getSession();
        $orders = $orderCollection->getOrders();

        $countProducts = 0;
        $order = $orderProduct->getOrder();

        if ($order) {
            $products = $order->getStoreOrdersProducts();
            $countProducts = count($products);
        }

        $collection = $orderCollection->getOrderProducts();

        $store['quantity'] = 0;
        $store['orders'] = [];

        if ($collection) {
            $store = [
                'quantity' => $collection['count'] ?: 0,
                'orders' => $collection['orders'] ?: [],
            ];
        }

        $session->set('quantity', $store['quantity']);

        return $this->json([
            'products' => $countProducts,
            'summary' => $orders['summary'] !== null ? $orderSummary->summary($orders['summary'], true) : [],
            'removed' => $orderProduct->getStore()->getId(),
            'redirect' => !($orders['summary'] !== null),
            'store' => [
                'quantity' => $session->get('quantity'),
                'orders' => $store['orders'],
            ],
            'redirectUrl' => $this->generateUrl('app_market_place_order_summary'),
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
     * @param UserInterface|null $user
     * @param UserManagerInterface $userManager
     * @param SummaryInterface $order
     * @param CollectionInterface $collection
     * @return Response
     */
    #[Route('/summary', name: 'app_market_place_order_summary', methods: ['GET'])]
    public function summary(
        Request              $request,
        ?UserInterface       $user,
        UserManagerInterface $userManager,
        SummaryInterface     $order,
        CollectionInterface  $collection,
    ): Response
    {
        $customer = $userManager->get($user);
        $orders = $collection->getOrders($customer);

        return $this->render('market_place/order/summary.html.twig', [
            'orders' => $orders['summary'] ?: null,
            'summary' => $orders['summary'] !== null ? $order->summary($orders['summary']) : null,
        ]);
    }

    /**
     * @param Request $request
     * @param CollectionInterface $order
     * @return JsonResponse
     */
    #[Route('/cart', name: 'app_market_place_product_order_cart', methods: ['POST', 'GET'])]
    public function cart(
        Request              $request,
        CollectionInterface  $order,
    ): JsonResponse
    {
        $collection = $order->collection();

        return $this->json([
            'template' => $this->renderView('market_place/cart.html.twig', ['orders' => $collection]),
            'quantity' => $request->getSession()->get('quantity'),
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param ProcessorInterface $processor
     * @param UserManagerInterface $userManager
     * @param CollectionInterface $collection
     * @return JsonResponse
     */
    #[Route('/{product}', name: 'app_market_place_product_order', methods: ['POST'])]
    public function order(
        Request              $request,
        ?UserInterface       $user,
        ProcessorInterface   $processor,
        UserManagerInterface $userManager,
        CollectionInterface  $collection,
    ): JsonResponse
    {
        $data = $request->toArray();

        if (!$data) {
            return $this->json([
                'error' => 'Invalid parameters provided.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $session = $request->getSession();

        $customer = $userManager->get($user);
        $order = $processor->findOrder();

        $processor->processOrder($order, $customer);
        $collection = $collection->getOrderProducts();

        $store = [
            'quantity' => $collection['count'],
        ];

        $session->set('quantity', $store['quantity']);

        return $this->json([
            'store' => [
                'quantity' => $session->get('quantity'),
            ],
        ]);
    }
}