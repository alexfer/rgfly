<?php declare(strict_types=1);

namespace App\Controller\MarketPlace\Cabinet;

use App\Controller\Trait\ControllerTrait;
use App\Entity\MarketPlace\StoreCustomerOrders;
use Pontedilana\PhpWeasyPrint\Pdf;
use Pontedilana\WeasyprintBundle\WeasyPrint\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/cabinet/invoice')]
class InvoiceController extends AbstractController
{
    use ControllerTrait;

    #[Route(path: '/get/{order}/{invoice}', name: 'app_marketplace_cabinet_invoice_download', methods: ['GET'])]
    public function download(Request $request, Pdf $pdf, ParameterBagInterface $params): PdfResponse
    {
        $customerOrder = $this->em->getRepository(StoreCustomerOrders::class)->findOneBy([
            'orders' => $request->get('order'),
            'customer' => $this->getCustomer($this->getUser())
        ]);

        if (!$customerOrder) {
            throw $this->createAccessDeniedException();
        }

        $order = $customerOrder->getOrders();
        $products = $order->getStoreOrdersProducts();

        $invoice = $order->getStoreInvoice();

        $html = $this->renderView(
            'market_place/cabinet/pdf/invoice.html.twig',
            [
                'invoice' => $invoice,
                'products' => $products,
                'order' => $order,
                'customer' => $customerOrder->getCustomer(),
                'path' => $params->get('kernel.project_dir'),
            ]
        );

        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            strtoupper($invoice->getNumber()) . '.pdf'
        );
    }
}