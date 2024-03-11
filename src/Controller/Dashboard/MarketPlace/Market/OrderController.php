<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\MarketOrders;
use App\Service\Dashboard;
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\MarketTrait;
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
    use Dashboard, MarketTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{market}', name: 'app_dashboard_market_place_order_market')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        $currency = Currency::currency($market->getCurrency());
        $orders = $em->getRepository(MarketOrders::class)->findBy(['market' => $market], ['id' => 'desc']);

        $summary = $products = [];
        foreach ($orders as $order) {
            foreach ($order->getMarketOrdersProducts() as $item) {
                $products[$order->getId()][] = $item->getCost() - ((($item->getCost() * $item->getQuantity()) * $item->getDiscount()) - $item->getDiscount()) / 100;
            }
            $summary[$order->getId()] = [
                'total' => array_sum($products[$order->getId()]),
            ];
        }

        return $this->render('dashboard/content/market_place/order/index.html.twig', $this->navbar() + [
                'market' => $market,
                'currency' => $currency,
                'orders' => $orders,
                'summary' => $summary,
            ]);
    }

    /**
     * @param Request $request
     * @param MarketOrders $order
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{market}/{number}', name: 'app_dashboard_market_place_order_details_market')]
    public function details(
        Request                $request,
        MarketOrders           $order,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $currency = Currency::currency($market->getCurrency());

        $products = [];
        foreach ($order->getMarketOrdersProducts() as $item) {
            $products[] = $item->getCost() - ((($item->getCost() * $item->getQuantity()) * $item->getDiscount()) - $item->getDiscount()) / 100;
        }

        return $this->render('dashboard/content/market_place/order/order.html.twig', $this->navbar() + [
                'market' => $market,
                'currency' => $currency,
                'country' => Countries::getNames(\Locale::getDefault()),
                'order' => $order,
                'total' => array_sum($products),
            ]);
    }
}