<?php

namespace App\Controller\MarketPlace;

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

#[Route('/market-place/order')]
class OrderController extends AbstractController
{

    public function checkout()
    {

    }

    #[Route('/summary', name: 'app_market_place_order_update', methods: ['POST'])]
    public function update(
        Request                $request,
        EntityManagerInterface $em,
    ): Response
    {
        dd($request->request->all());
        return $this->redirectToRoute('app_market_place_order_summary');
    }

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
     * @return JsonResponse
     */
    #[Route('/cart', name: 'app_market_place_product_order_cart', methods: ['POST', 'GET'])]
    public function cart(Request $request): JsonResponse
    {
        $session = $request->getSession();

        return $this->json([
            'orders' => unserialize($session->get('orders')),
            'template' => $this->renderView('market_place/cart.html.twig', ['orders' => unserialize($session->get('orders'))]),
            'quantity' => $session->get('quantity') ?? 0,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/{product}', name: 'app_market_place_product_order', methods: ['POST'])]
    public function order(
        Request                $request,
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

        $discount = fn($cost) => $cost - (($cost * $product->getDiscount()) - $product->getDiscount()) / 100;

        if (!$order) {
            $order = new MarketOrders();
            $order->setMarket($market)
                ->setSession($session->getId())
                ->setDiscount($discount($product->getCost()))
                ->setTotal($product->getCost());

            $em->persist($order);

            $orderCustomer = new MarketCustomerOrders();
            $orderCustomer->setOrders($order)->setCustomer(null);
            $em->persist($orderCustomer);

            $orderProducts = new MarketOrdersProduct();
            $orderProducts->setOrders($order)
                ->setColor($data['color'])
                ->setSize($data['size'])
                ->setProduct($product)
                ->getOrders()
                ->setNumber(MarketPlaceHelper::slug(1, 10, 'o'));

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
                ->setProduct($product);

            $em->persist($orderProducts);
            $em->flush();
            $session->set('quantity', $session->get('quantity') + 1);
        }

        $orders = $em->getRepository(MarketOrders::class)->findBy(['session' => $session->getId()]);
        $collection = $em->getRepository(MarketOrders::class)->getSerializedData($orders);

        if ($session->has('orders')) {
            $session->remove('orders');
        }
        $session->set('orders', serialize($collection));

        return $this->json([
            'quantity' => $session->get('quantity') ?? 1,
        ]);
    }
}