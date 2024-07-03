<?php

namespace App\Controller\Dashboard;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomerOrders;
use App\Entity\MarketPlace\StoreOrders;
use App\Entity\User;
use App\Helper\MarketPlace\MarketPlaceHelper;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/dashboard')]
class IndexController extends AbstractController
{

    public function __construct(private readonly ChartBuilderInterface $chartBuilder)
    {

    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws Exception
     */
    #[Route('/{slug?}/{period?}/{month?}/{day?}', name: 'app_dashboard')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $period = $request->get('period');
        $slug = $request->get('slug');
        $month = $request->get('month');
        $day = $request->get('day');
        $criteria = ['owner' => $user];
        $ids = [];
        $adminStore= $chart = $m = null;

        $year = date('Y');
        $currentMonth = strtolower(date('F'));
        $months = MarketPlaceHelper::months(date('m'));

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
        $repository = $em->getRepository(StoreOrders::class);

        if($period == 'month') {
            $dmonth = $month?:date('m');
            $date = new \DateTimeImmutable("$year-$dmonth-01 00:00:00");
            $m = $date->format('m');
        }

        $total = $repository->summaryOrders($store, $year, $m);
        $sum = $repository->summarySum($store, $year, $m);


        if ($period == 'month') {
            $orders = $repository->backdropOrderSummaryByMonths($store, $year, $month ?: $currentMonth, $months);
        } else {
            $orders = $repository->backdropOrderSummaryByYear($store, $year);
        }

        if (count($orders) > 0) {
            $chart = $this->chartBuilder($orders, $period, $translator);
        }

        $orders = $repository->orders($store);

        foreach ($orders as $order) {
            $ids[$order['id']] = $order['id'];
        }

        $customers = $em->getRepository(StoreCustomerOrders::class)->summaryCustomers($ids);

        return $this->render('dashboard/content/index.html.twig', [
            'chart' => $chart ?: null,
            'stores' => $stores,
            'orders' => [
                'total' => $total,
            ],
            'sum' => $sum,
            'months' => $months,
            'planedProfit' => 250000,
            'customers' => [
                'total' => count($customers),
            ],
            'store' => $store,
        ]);
    }

    /**
     * @param array $orders
     * @param string|null $period
     * @param TranslatorInterface $translator
     * @return Chart
     */
    private function chartBuilder(
        array               $orders,
        ?string             $period,
        TranslatorInterface $translator,
    ): Chart
    {
        $data = [];
        foreach ($orders as $order) {
            $data['total'][] = $order['total'];
            $data['months'][] = $order['month'];
        }


        if ($period == 'month') {
            $period = range(1, date('t'));
        } else {
            $period = $data['months'];
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $chart->setData([
            'labels' => $period,
            'datasets' => [
                [
                    'label' => $translator->trans('table.header.total'),
                    'backgroundColor' => 'rgba(11,142,33,0.8)',
                    'borderColor' => 'rgba(11,142,33,0.8)',
                    'data' => $data['total'],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'x' => [
                    'suggestedMin' => min($data['total']),
                    'suggestedMax' => max($data['total']),
                ],
            ],
        ]);

        return $chart;
    }
}
