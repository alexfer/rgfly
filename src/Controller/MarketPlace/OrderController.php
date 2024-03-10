<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketCustomerOrders;
use App\Entity\MarketPlace\MarketOrders;
use App\Entity\MarketPlace\MarketOrdersProduct;
use App\Entity\MarketPlace\MarketProduct;
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Repository\MarketPlace\MarketOrdersProductRepository;
use App\Service\MarketPlace\Currency;
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
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/summary/remove', name: 'app_market_place_order_remove_product', methods: ['POST'])]
    public function remove(
        Request                $request,
        ?UserInterface         $user,
        EntityManagerInterface $em,
    ): Response
    {
        $payload = $request->getPayload()->all();

        $productsRepository = $em->getRepository(MarketOrdersProduct::class);

        $customer = $em->getRepository(MarketCustomer::class)->findOneBy(['member' => $user]);
        $market = $em->getRepository(Market::class)->find($payload['market']);
        $order = $em->getRepository(MarketOrders::class)->findOneBy(['session' => $payload['order'], 'market' => $market]);
        $product = $productsRepository->find($payload['product']);
        $customerOrder = $em->getRepository(MarketCustomerOrders::class)->findOneBy(['orders' => $order, 'customer' => $customer]);
        $products = $productsRepository->findBy(['orders' => $order]);
        $order->removeMarketOrdersProduct($product);
        $em->remove($product);

        $removed = false;
        if (count($products) == 1) {
            $order->removeMarketCustomerOrder($customerOrder);
            $em->remove($customerOrder);
            $em->remove($order);
            $removed = true;
        } else {
            $rewind = $order->getTotal() - ($product->getCost() * $product->getQuantity());
            $order->setTotal($rewind);
            $em->persist($order);
        }
        $em->flush();

        $session = $request->getSession();
        $orders = $em->getRepository(MarketOrders::class)->findBy(['session' => $session->getId()]);

        $session->set('quantity', count($orders));

        return $this->json([
            'products' => count($products),
            'summary' => $this->getSummary($orders, true),
            'removed' => $market->getId(),
            'order' => count($orders) == 0,
            'quantity' => $session->get('quantity'),
            'redirect' => $this->generateUrl('app_market_place_order_summary'),
        ]);
    }

    /**
     * @param array $orders
     * @param bool $formatted
     * @return array
     */
    private function getSummary(array $orders, bool $formatted = false): array
    {
        $summary = [];
        foreach ($orders as $order) {
            $products = $order->getMarketOrdersProducts()->toArray();
            $itemSubtotal = $fee = $itemSubtotalDiscount = [];
            foreach ($products as $product) {
                $cost = $product->getCost() * $product->getQuantity();
                $discount = $product->getDiscount();
                $fee[$order->getId()][] = $product->getProduct()->getFee();
                $itemSubtotal[$order->getId()][] = $product->getCost() * $product->getQuantity();
                $itemSubtotalDiscount[$order->getId()][] = $cost - (($cost * $discount) - $discount) / 100;
            }

            if($formatted) {
                $summary[] = [
                    'market' => $order->getMarket()->getId(),
                    'currency' => Currency::currency($order->getMarket()->getCurrency())['symbol'],
                    'fee' => number_format(array_sum($fee[$order->getId()]), 2, '.', ' '),
                    'total' => number_format(round(array_sum($fee[$order->getId()]) + array_sum($itemSubtotalDiscount[$order->getId()])), 2, '.', ' '),
                    'itemSubtotal' => number_format(round(array_sum($itemSubtotal[$order->getId()])), 2, '.', ' '),
                    'itemSubtotalDiscount' => number_format(round(array_sum($itemSubtotalDiscount[$order->getId()])), 2, '.', ' '),
                ];
            } else {
                $summary[] = [
                    'number' => $order->getNumber(),
                    'market_id' => $order->getMarket()->getId(),
                    'market_name' => $order->getMarket()->getName(),
                    'currency' => Currency::currency($order->getMarket()->getCurrency())['symbol'],
                    'fee' => array_sum($fee[$order->getId()]),
                    'total' => $order->getTotal(),
                    'itemSubtotal' => round(array_sum($itemSubtotal[$order->getId()])),
                    'itemSubtotalDiscount' => round(array_sum($itemSubtotalDiscount[$order->getId()])),
                ];
            }
        }
        return $summary;
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/summary', name: 'app_market_place_order_update', methods: ['POST'])]
    public function update(
        Request                $request,
        EntityManagerInterface $em,
    ): Response
    {
        $input = $request->request->all();
        $session = $request->getSession();

        foreach ($input['order']['product'] as $k => $v) {
            $orders = $em->getRepository(MarketOrders::class)->findOneBy(['id' => $input['order'][$k], 'session' => $session->getId()]);
            $product = $em->getRepository(MarketOrdersProduct::class)->findOneBy(['id' => $v, 'orders' => $orders]);
            $product->setQuantity($input['order']['quantity'][$k]);
            $em->persist($product);
            $em->flush();
        }
        return $this->redirectToRoute('app_market_place_order_summary');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/summary', name: 'app_market_place_order_summary', methods: ['GET'])]
    public function summary(
        Request                       $request,
        EntityManagerInterface        $em,
        MarketOrdersProductRepository $repository,
    ): Response
    {
        $session = $request->getSession();
        $orders = $em->getRepository(MarketOrders::class)->findBy(['session' => $session->getId()]);

        return $this->render('market_place/order/summary.html.twig', [
            'orders' => $orders,
            'summary' => $this->getSummary($orders),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/cart', name: 'app_market_place_product_order_cart', methods: ['POST', 'GET'])]
    public function cart(
        Request                $request,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $session = $request->getSession();

        $orders = $em->getRepository(MarketOrders::class)->findBy(['session' => $session->getId()]);
        $collection = $em->getRepository(MarketOrders::class)->getSerializedData($orders);

        return $this->json([
            'template' => $this->renderView('market_place/cart.html.twig', ['orders' => $collection]),
            'quantity' => $session->get('quantity') ?: 0,
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/{product}', name: 'app_market_place_product_order', methods: ['POST'])]
    public function order(
        Request                $request,
        ?UserInterface         $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $data = $request->toArray();

        if (!$data) {
            return $this->json([
                'request' => $request->toArray(),
            ]);
        }

        $session = $request->getSession();

        if (!$session->isStarted()) {
            $session->start();
        }

        $customer = $em->getRepository(MarketCustomer::class)->findOneBy(['member' => $user]);
        $product = $em->getRepository(MarketProduct::class)->findOneBy(['slug' => $request->get('product')]);
        $market = $product->getMarket();

        $order = $em->getRepository(MarketOrders::class)->findOneBy([
            'market' => $market,
            'session' => $session->getId(),
        ]);

        $orderProducts = $em->getRepository(MarketOrdersProduct::class)->findOneBy([
            'product' => $product,
            'size' => $data['size'],
            'color' => $data['color'],
        ]);

        $productDiscount = $product->getDiscount();
        $discount = fn($cost) => $cost - (($cost * $productDiscount) * $productDiscount) / 100;

        if (!$order) {
            $order = new MarketOrders();
            $order->setMarket($market)
                ->setSession($session->getId())
                ->setTotal($product->getCost());

            $em->persist($order);

            $orderCustomer = new MarketCustomerOrders();
            $orderCustomer->setOrders($order)->setCustomer($customer);
            $em->persist($orderCustomer);

            $orderProducts = new MarketOrdersProduct();
            $orderProducts->setOrders($order)
                ->setColor($data['color'])
                ->setSize($data['size'])
                ->setProduct($product)
                ->setCost($product->getCost())
                ->setDiscount($product->getDiscount())
                ->getOrders()
                ->setNumber(MarketPlaceHelper::slug($order->getId(), 10, 'o'));

            $em->persist($orderProducts);
            $em->flush();

            $session->set('quantity', $session->get('quantity') + 1);
        }

        if (!$orderProducts) {

            $order->setTotal($order->getTotal() + $product->getCost())
                ->setSession($session->getId());
            $em->persist($order);

            $orderProducts = new MarketOrdersProduct();
            $orderProducts->setOrders($order)
                ->setColor($data['color'])
                ->setSize($data['size'])
                ->setCost($product->getCost())
                ->setDiscount($product->getDiscount())
                ->setProduct($product);

            $em->persist($orderProducts);
            $em->flush();
            $session->set('quantity', $session->get('quantity') + 1);
        }

        $orders = $em->getRepository(MarketOrders::class)->findBy(['session' => $session->getId()]);

        $serialized = [];

        foreach ($orders as $order) {
            $serialized[$order->getId()] = $order->getNumber();
        }

        $session->set('orders', serialize($serialized));

        return $this->json([
            'quantity' => $session->get('quantity') ?? 1,
        ]);
    }
}