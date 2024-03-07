<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketCustomerOrders;
use App\Entity\MarketPlace\MarketOrders;
use App\Entity\MarketPlace\MarketOrdersProduct;
use App\Entity\MarketPlace\MarketProduct;
use App\Helper\MarketPlace\MarketPlaceHelper;
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

        if (count($products) == 1) {
            $order->removeMarketCustomerOrder($customerOrder);
            $em->remove($customerOrder);
            $em->remove($order);
        } else {
            $order->setTotal($order->getTotal() - $product->getCost())->setDiscount($order->getDiscount() - $product->getCost());
            $em->persist($order);
        }

        $em->flush();

        $session = $request->getSession();

        $summary = [];
        $orders = $em->getRepository(MarketOrders::class)->findBy(['session' => $session->getId()]);

        foreach($orders as $k => $v) {
            $products = $v->getMarketOrdersProducts()->toArray();
            $arrayProducts = [];
            foreach ($products as $product) {
                $arrayProducts[$product->getOrders()->getId()] = [
                    'cost' => $product->getCost(),
                    'discount' => $product->getDiscount(),
                    'quantity' => $product->getQuantity(),
                ];
            }
            $summary[$v->getMarket()->getId()] = [
                'market' => $v->getMarket()->getId(),
                'total' => $v->getTotal(),
                'discount' => $v->getDiscount(),
                'products' => $arrayProducts,
            ];
        }

        $session->set('quantity', count($orders));

        return $this->json([
            'product' => true,
            'summary' => [
                'summary' => $summary,

            ],
            'order' => count($orders) == 0,
            'quantity' => $session->get('quantity'),
            'redirect' => $this->generateUrl('app_market_place_order_summary'),
        ]);
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
        Request                $request,
        EntityManagerInterface $em,
    ): Response
    {
        $session = $request->getSession();
        $orders = $em->getRepository(MarketOrders::class)->findBy(['session' => $session->getId()]);

        return $this->render('market_place/order/summary.html.twig', [
            'orders' => $orders,
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
        $discount = fn($cost) => $cost - (($cost * $productDiscount) - $productDiscount) / 100;

        if (!$order) {
            $order = new MarketOrders();
            $order->setMarket($market)
                ->setSession($session->getId())
                ->setDiscount($discount($product->getCost()))
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
                ->setDiscount($order->getDiscount() + $discount($product->getCost()))
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