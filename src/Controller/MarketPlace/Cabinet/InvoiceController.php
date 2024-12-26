<?php declare(strict_types=1);

namespace App\Controller\MarketPlace\Cabinet;

use App\Controller\Trait\ControllerTrait;
use App\Entity\MarketPlace\StoreCustomerOrders;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/cabinet/invoice')]
class InvoiceController extends AbstractController
{
    use ControllerTrait;

    #[Route(path: '/get/{order}/{invoice}', name: 'app_marketplace_cabinet_invoice_download', methods: ['GET'])]
    public function download(Request $request): Response
    {
        $customerOrder = $this->em->getRepository(StoreCustomerOrders::class)->findOneBy([
            'orders' => $request->get('order'),
            'customer' => $this->getCustomer($this->getUser())
        ]);

        if(!$customerOrder) {
            throw $this->createAccessDeniedException();
        }

        $order = $customerOrder->getOrders();
        $products = $order->getStoreOrdersProducts();
        $invoice = $order->getStoreInvoice();
        dd($products);
    }
}