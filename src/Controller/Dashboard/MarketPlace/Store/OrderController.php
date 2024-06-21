<?php

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\{Store, StoreOrders};
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\Dashboard\Store\Interface\ServeStoreInterface as StoreInterface;
use App\Service\MarketPlace\StoreTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Intl\Countries;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/market-place/order')]
class OrderController extends AbstractController
{
    use StoreTrait;

    /**
     * @param UserInterface $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_dashboard_market_place_order_stores')]
    public function index(
        UserInterface          $user,
        Request                $request,
        EntityManagerInterface $manager,
    ): Response
    {
        $stores = $manager->getRepository(Store::class)->stores($user);

        return $this->render('dashboard/content/market_place/order/stores.html.twig', [
            'stores' => $stores['result'],
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param StoreInterface $serveStore
     * @return Response
     */
    #[Route('/{store}', name: 'app_dashboard_market_place_order_store_current')]
    public function current(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        StoreInterface         $serveStore,
    ): Response
    {
        $store = $this->store($serveStore, $user);

        $currency = Currency::currency($store->getCurrency());
        $orders = $em->getRepository(StoreOrders::class)->findBy(['store' => $store], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/order/index.html.twig', [
            'store' => $store,
            'currency' => $currency,
            'orders' => $orders,
        ]);
    }

    /**
     * @param Request $request
     * @param StoreOrders $order
     * @param UserInterface $user
     * @param StoreInterface $serveStore
     * @return Response
     */
    #[Route('/{store}/{number}', name: 'app_dashboard_market_place_order_details_market')]
    public function details(
        Request        $request,
        StoreOrders    $order,
        UserInterface  $user,
        StoreInterface $serveStore,
    ): Response
    {
        $store = $this->store($serveStore, $user);
        $currency = Currency::currency($store->getCurrency());

        $products = $fee = $itemSubtotal = [];
        foreach ($order->getStoreOrdersProducts() as $item) {
            $cost = round($item->getProduct()->getCost(), 2) + round($item->getProduct()->getFee(), 2);
            $discount = $item->getProduct()->getDiscount();
            $itemSubtotal[] = $item->getQuantity() * ($cost - (($cost * $discount) - $discount) / 100);
        }

        return $this->render('dashboard/content/market_place/order/order.html.twig', [
            'store' => $store,
            'currency' => $currency,
            'country' => Countries::getNames(\Locale::getDefault()),
            'order' => $order,
            'itemSubtotal' => array_sum($itemSubtotal),
        ]);
    }
}