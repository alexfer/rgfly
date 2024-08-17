<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreProduct;
use App\Service\MarketPlace\Dashboard\Operation\Interface\OperationInterface;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/marker-place/operation')]
class OperationController extends AbstractController
{
    /**
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_dashboard_market_place_operation')]
    public function index(
        EntityManagerInterface $manager,
    ): Response
    {
        $stores = $manager->getRepository(Store::class)->stores($this->getUser());

        return $this->render('dashboard/content/market_place/operation/index.html.twig', [
            'stores' => $stores['result'],
        ]);
    }

    #[Route('/{store}/import', name: 'app_dashboard_market_place_operation_import', methods: ['GET', 'POST'])]
    public function import()
    {

    }

    #[Route('/{store}/export', name: 'app_dashboard_market_place_operation_export', methods: ['GET', 'POST'])]
    public function export(
        Request            $request,
        OperationInterface $operation
    ): Response
    {
        if($request->isMethod('POST')) {
            $format = $request->request->get('format');
            $operation = $operation->export(StoreProduct::class, $format, (int)$request->get('store'));
            if($operation) {
                return $this->redirectToRoute('app_dashboard_market_place_operation');
            }
        }

        return $this->render('dashboard/content/market_place/operation/export.html.twig', []);
    }
}