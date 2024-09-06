<?php declare(strict_types=1);

namespace App\Controller\MarketPlace;

use App\Service\MarketPlace\Store\Customer\Interface\UserManagerInterface;
use App\Service\MarketPlace\Store\Order\Interface\{CollectionInterface,
    ComputeInterface,
    ProcessorInterface,
    ProductInterface,
    SummaryInterface};
use App\Storage\MarketPlace\FrontSessionHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Cookie, JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/order')]
class OrderController extends AbstractController
{

    /**
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @param SummaryInterface $orderSummary
     * @param CollectionInterface $orderCollection
     * @param ProductInterface $orderProduct
     * @return Response
     */
    #[Route('/summary/remove', name: 'app_market_place_order_remove_product', methods: ['POST'])]
    public function remove(
        Request              $request,
        UserManagerInterface $userManager,
        SummaryInterface     $orderSummary,
        CollectionInterface  $orderCollection,
        ProductInterface     $orderProduct,
    ): Response
    {
        $customer = $userManager->get($this->getUser());
        $orderProduct->process($customer);
        $orders = $orderCollection->getOrders(null, $this->getSessionId($request));

        $countProducts = 0;
        $order = $orderProduct->getOrder();

        if ($order) {
            $products = $order->getStoreOrdersProducts();
            $countProducts = count($products);
        }

        $collection = $orderCollection->getOrderProducts($this->getSessionId($request));
        $collection['quantity'] = $collection['quantity'] ?? 0;

        return $this->json([
            'products' => $countProducts,
            'summary' => $orders['summary'] !== null ? $orderSummary->summary($orders['summary'], true) : [],
            'removed' => $orderProduct->getStore()->getId(),
            'redirect' => !($orders['summary'] !== null),
            'url' => $this->generateUrl('app_market_place_shop_cookie_remove'),
            'store' => [
                'quantity' => $collection['quantity'],
                'orders' => isset($collection['clientOrders']) ?: [],
            ],
            'redirectUrl' => $this->generateUrl('app_market_place_index'),
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
     * @param UserManagerInterface $userManager
     * @param SummaryInterface $order
     * @param CollectionInterface $collection
     * @return Response
     */
    #[Route('/summary', name: 'app_market_place_order_summary', methods: ['GET'])]
    public function summary(
        Request              $request,
        UserManagerInterface $userManager,
        SummaryInterface     $order,
        CollectionInterface  $collection,
    ): Response
    {
        $sessionId = $this->getSessionId($request);

        if (!$sessionId) {
            return $this->redirectToRoute('app_market_place_order_summary');
        }

        $customer = $userManager->get($this->getUser());
        $orders = $collection->getOrders($customer, $sessionId);

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
    #[Route('/cart', name: 'app_market_place_product_order_cart')]
    public function cart(
        Request             $request,
        CollectionInterface $order,
    ): JsonResponse
    {
        $collection = $order->collection($this->getSessionId($request));

        return $this->json([
            'template' => $this->renderView('market_place/cart.html.twig', ['orders' => $collection['orders'] ?? []]),
            'quantity' => $collection['quantity'] ?? 0,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/cookie-remove', name: 'app_market_place_shop_cookie_remove', methods: ['OPTIONS'])]
    public function removeCookie(): Response
    {
        $response = new Response();
        $response->headers->clearCookie(FrontSessionHandler::NAME);
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/cookie', name: 'app_market_place_shop_cookie', methods: ['OPTIONS'])]
    public function setCookie(Request $request): Response
    {
        $response = new Response();

        if ($this->getSessionId($request)) {
            return $response->send();
        }

        $payload = $request->getPayload()->all();
        $cookie = Cookie::create(FrontSessionHandler::NAME)
            ->withValue($payload['session'])
            ->withExpires(strtotime('+1day'));

        $response->headers->setCookie($cookie);
        return $response;
    }

    /**
     * @param Request $request
     * @param ProcessorInterface $processor
     * @param UserManagerInterface $userManager
     * @param CollectionInterface $collection
     * @return JsonResponse
     */
    #[Route('/{product}', name: 'app_market_place_product_order', methods: ['POST'])]
    public function order(
        Request              $request,
        ProcessorInterface   $processor,
        UserManagerInterface $userManager,
        CollectionInterface  $collection
    ): JsonResponse
    {
        $data = $request->toArray();

        if (!$data) {
            return $this->json([
                'error' => 'Invalid parameters provided.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $customer = $userManager->get($this->getUser());
        $order = $processor->findOrder(null, $this->getSessionId($request));
        $processor->processOrder($order, $customer);
        $sessionId = $processor->getSessionId();
        $collection = $collection->getOrderProducts($sessionId);

        return $this->json([
            'store' => [
                'url' => $this->generateUrl('app_market_place_shop_cookie'),
                'session' => $sessionId,
                'quantity' => $collection['quantity'] ?: 1,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return string|null
     */
    private function getSessionId(Request $request): ?string
    {
        return $request->cookies->get(FrontSessionHandler::NAME);
    }
}