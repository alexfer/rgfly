<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketOrders;
use App\Entity\MarketPlace\MarketOrdersProduct;
use App\Entity\MarketPlace\MarketProduct;
use App\Helper\MarketPlace\MarketPlaceHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/order')]
class OrderController extends AbstractController
{
    #[Route('/{product}', name: 'app_market_place_product_order', methods: ['POST'])]
    public function order(Request $request, EntityManagerInterface $em): Response
    {
        $data = $request->toArray();

        $product = $em->getRepository(MarketProduct::class)->findOneBy(['slug' => $request->get('product')]);
        $market = $product->getMarket();
        $order = $em->getRepository(MarketOrders::class)->findOneBy(['market' => $market]);
        $marketOrder = $em->getRepository(MarketOrdersProduct::class)->findOneBy([
            'product' => $product,
            'size' => $data['size'],
            'color' => $data['color'],
        ]);

        $session = $request->getSession();

        if (!$order) {
            $order = new MarketOrders();
            $order->setMarket($market)->setTotal($product->getCost());
            $em->persist($order);

            $marketOrder = new MarketOrdersProduct();
            $marketOrder->setOrders($order)
                ->setColor($data['color'])
                ->setSize($data['size'])
                ->setProduct($product)
                ->getOrders()
                ->setNumber(MarketPlaceHelper::slug(1, 10, 'o'));

            $em->persist($marketOrder);
            $em->flush();
        }

        if(!$marketOrder) {
            $order->setTotal($order->getTotal() + $product->getCost());
            $em->persist($order);

            $marketOrder = new MarketOrdersProduct();
            $marketOrder->setOrders($order)
                ->setColor($data['color'])
                ->setSize($data['size'])
                ->setProduct($product);

            $em->persist($marketOrder);
            $em->flush();
        }

        $quantity = $order->getMarketOrdersProducts()->count();
        $session->set('quantity', $quantity);

        return $this->json([
            //'session' => $session->get('order'),
            'order' => $order->getNumber(),
            'product' => $product->getSlug(),
            'size' => $marketOrder->getSize(),
            'color' => $marketOrder->getColor(),
            'quantity' => $quantity,
        ]);
    }
}