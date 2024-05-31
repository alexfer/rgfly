<?php

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\StoreOrders;
use App\Service\Dashboard;
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\StoreTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/market-place/order')]
class OrderController extends AbstractController
{
    use Dashboard, StoreTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{store}', name: 'app_dashboard_market_place_order_market')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);

        $currency = Currency::currency($store->getCurrency());
        $orders = $em->getRepository(StoreOrders::class)->findBy(['store' => $store], ['id' => 'desc']);

        $summary = $products = [];
        foreach ($orders as $order) {
            foreach ($order->getStoreOrdersProducts() as $item) {
                $products[$order->getId()][] = $item->getCost() - ((($item->getCost() * $item->getQuantity()) * $item->getDiscount()) - $item->getDiscount()) / 100;
            }
            $summary[$order->getId()] = [
                'total' => array_sum($products[$order->getId()]) != $order->getTotal() ? $order->getTotal() : array_sum($products[$order->getId()]),
            ];
        }

        return $this->render('dashboard/content/market_place/order/index.html.twig',  [
                'store' => $store,
                'currency' => $currency,
                'orders' => $orders,
                'summary' => $summary,
            ]);
    }

    /**
     * @param Request $request
     * @param StoreOrders $order
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{store}/{number}', name: 'app_dashboard_market_place_order_details_market')]
    public function details(
        Request                $request,
        StoreOrders            $order,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $currency = Currency::currency($store->getCurrency());

        $products = $fee = $itemSubtotal = [];
        foreach ($order->getStoreOrdersProducts() as $item) {
            $amountDiscount = $item->getCost() - ((($item->getCost() * $item->getQuantity()) * $item->getDiscount()) - $item->getDiscount()) / 100;
            $products[] = $amountDiscount;
            $itemSubtotal[] = $amountDiscount;
            $fee[] = $item->getProduct()->getFee();
        }

        return $this->render('dashboard/content/market_place/order/order.html.twig',  [
                'store' => $store,
                'currency' => $currency,
                'country' => Countries::getNames(\Locale::getDefault()),
                'order' => $order,
                'total' => array_sum($products),
                'fee' => array_sum($fee),
                'itemSubtotal' => array_sum($itemSubtotal),
            ]);
    }
}