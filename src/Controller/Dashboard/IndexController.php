<?php

namespace App\Controller\Dashboard;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomerOrders;
use App\Entity\MarketPlace\StoreOrders;
use App\Entity\User;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

#[Route('/dashboard')]
class IndexController extends AbstractController
{

    /**
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('/{slug?}/{period?}', name: 'app_dashboard')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        ChartBuilderInterface  $chartBuilder,
        TranslatorInterface    $translator,
    ): Response
    {
        $period = $request->get('period');
        $slug = $request->get('slug');
        $chart = null;
        $periods = [];

        if (!$period) {
            $periods = ['yearly', 'monthly', 'daily'];

        } else {
//            if(in_array(User::ROLE_ADMIN, $user->getRoles())){
//                $store = $em->getRepository(Store::class)->findStoreSummaryBySlug($request->get('slug'));
//            }
//            $store = $em->getRepository(Store::class)->findBy(['owner' => $user]);
//            $store = reset($store);
//            $data = [];
//
//            $orders = $em->getRepository(StoreOrders::class)->findBy(['store' => $store], ['created_at' => 'ASC']);
//
//            foreach ($orders as $order) {
//                $data['total']['comparison'][] = $order->getTotal();
//                if ($order->getStatus() == StoreOrders::STATUS['confirmed']) {
//                    $data['total']['confirmed'][] = $order->getTotal();
//                }
//
//                if ($order->getStatus() == StoreOrders::STATUS['processing']) {
//                    $data['total']['processing'][] = $order->getTotal();
//                }
//            }
//
//            $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
//            $chart->setData([
//                'labels' => ['February', 'March', 'April', 'May', 'June'],
//                'datasets' => [
//                    [
//                        'label' => ucfirst(StoreOrders::STATUS['confirmed']),
//                        'backgroundColor' => 'rgba(253, 13, 77, 0.75)',
//                        'borderColor' => 'rgba(253, 13, 77, 0.75)',
//                        'data' => $data['total']['confirmed'],
//                    ],
//                    [
//                        'label' => ucfirst(StoreOrders::STATUS['processing']),
//                        'backgroundColor' => 'rgba(60, 60, 62, 0.75)',
//                        'borderColor' => 'rgba(60, 60, 62, 0.75)',
//                        'data' => $data['total']['processing'],
//                    ],
//                ],
//            ]);
//
//            $chart->setOptions([
//                'scales' => [
//                    'y' => [
//                        'suggestedMin' => min($data['total']['comparison']),
//                        'suggestedMax' => max($data['total']['comparison']),
//                    ],
//                ],
//            ]);
        }

        $criteria = ['owner' => $user];
        $ids = $invoices = [];
        $adminStore = null;

        if ($slug) {
            $criteria['slug'] = $slug;
        }

        if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
            if ($slug) {
                $adminStore = $em->getRepository(Store::class)->findOneBy(['slug' => $slug]);
            }
            $stores = $em->getRepository(Store::class)->findAll();

        } else {
            $stores = $em->getRepository(Store::class)->findBy($criteria);
        }

        $store = $adminStore ?: reset($stores);

        $orders = $em->getRepository(StoreOrders::class)->findBy(['store' => $store, 'session' => null], ['created_at' => 'ASC']);

        foreach ($orders as $order) {
            $ids[] = $order->getId();
            $invoices[] = $order->getStoreInvoice();
        }

        $customers = $em->getRepository(StoreCustomerOrders::class)->findOneBy(['orders' => $ids]);

        return $this->render('dashboard/content/index.html.twig', [
            'chart' => $chart !== null ? $chart : null,
            'periods' => count($periods) ? $periods : null,
            'stores' => $stores,
            'orders' => $orders,
            'invoices' => count($invoices),
            'customers' => $customers,
            'store' => $store,
        ]);
    }
}
