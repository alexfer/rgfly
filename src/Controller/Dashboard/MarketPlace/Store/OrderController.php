<?php declare(strict_types=1);

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\{Enum\EnumStoreOrderStatus, Store, StoreOrders};
use App\Helper\MarketPlace\MarketPlaceHelper;
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
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_dashboard_market_place_order_stores')]
    public function index(
        UserInterface          $user,
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

        $pagination = $this->paginator->paginate(
            $orders,
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        return $this->render('dashboard/content/market_place/order/index.html.twig', [
            'store' => $store,
            'currency' => $currency,
            'orders' => $pagination,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @param StoreInterface $serveStore
     * @return Response
     */
    #[Route('/{store}/{number}', name: 'app_dashboard_market_place_order_details_market')]
    public function details(
        Request                $request,
        EntityManagerInterface $em,
        UserInterface          $user,
        StoreInterface         $serveStore,
    ): Response
    {
        $store = $this->store($serveStore, $user);
        $currency = Currency::currency($store->getCurrency());

        $order = $em->getRepository(StoreOrders::class)->findOneBy(['store' => $store, 'number' => $request->get('number')]);

        if ($order->getStatus()->value != EnumStoreOrderStatus::Confirmed->value) {
            return $this->redirectToRoute('app_dashboard_market_place_order_store_current', [
                'store' => $store->getId(),
            ]);
        }

        $itemSubtotal = [];

        foreach ($order->getStoreOrdersProducts() as $item) {
            $discount = MarketPlaceHelper::discount(
                $item->getProduct()->getCost(),
                $item->getProduct()->getStoreProductDiscount()->getValue(),
                $item->getProduct()->getFee(),
                $item->getQuantity(),
                $item->getProduct()->getStoreProductDiscount()->getUnit()
            );
            $itemSubtotal[] = $discount;
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