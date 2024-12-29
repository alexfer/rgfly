<?php declare(strict_types=1);

namespace Essence\Controller\MarketPlace\Cabinet;

use Essence\Controller\Trait\ControllerTrait;
use Essence\Entity\MarketPlace\StoreCustomerOrders;
use Essence\Service\MarketPlace\Mail\SendMailInterface;
use Pontedilana\PhpWeasyPrint\Pdf;
use Pontedilana\WeasyprintBundle\WeasyPrint\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\{Countries, Locale,};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/cabinet/invoice')]
class InvoiceController extends AbstractController
{
    use ControllerTrait;

    /**
     * @param Request $request
     * @param Pdf $pdf
     * @param ParameterBagInterface $params
     * @return PdfResponse
     */
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
        $invoice = $order->getStoreInvoice();
        $embed = null;

        if ($order->getStore()->getAttach()) {
            $embed = '/public/' . $params->get('market_storage_logo') . '/' . $order->getStore()->getId() . '/' . $order->getStore()->getAttach()->getName();
        }

        $html = $this->renderView(
            'market_place/cabinet/pdf/invoice.html.twig',
            [
                'countries' => Countries::getNames(Locale::getDefault()),
                'invoice' => $invoice,
                'products' => $order->getStoreOrdersProducts(),
                'order' => $order,
                'customer' => $customerOrder->getCustomer(),
                'path' => $params->get('kernel.project_dir'),
                'embed' => $embed,
                'embedded' => false,
            ]
        );

        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            strtoupper($invoice->getNumber()) . '.pdf'
        );
    }

    /**
     * @param Request $request
     * @param Pdf $pdf
     * @param ParameterBagInterface $params
     * @param TranslatorInterface $translator
     * @param SendMailInterface $mail
     * @return JsonResponse
     */
    #[Route(path: '/send/{order}/{invoice}', name: 'app_marketplace_cabinet_invoice_send', methods: ['POST'])]
    public function send(
        Request               $request,
        Pdf                   $pdf,
        ParameterBagInterface $params,
        TranslatorInterface   $translator,
        SendMailInterface     $mail
    ): JsonResponse
    {
        $customerOrder = $this->em->getRepository(StoreCustomerOrders::class)->findOneBy([
            'orders' => $request->get('order'),
            'customer' => $this->getCustomer($this->getUser())
        ]);

        if (!$customerOrder) {
            throw $this->createAccessDeniedException();
        }

        $order = $customerOrder->getOrders();
        $invoice = $order->getStoreInvoice();

        $output = $params->get('invoice_pdf') . '/' . strtoupper($invoice->getNumber()) . '.pdf';

        $path = $params->get('kernel.project_dir') . '/public/img/essence.svg';

        if ($order->getStore()->getAttach()) {
            $path = $params->get('kernel.project_dir') . '/public/' . $params->get('market_storage_logo') . '/' . $order->getStore()->getId() . '/' . $order->getStore()->getAttach()->getName();
        }

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $embed = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $html = $this->renderView(
            'market_place/cabinet/pdf/invoice.html.twig',
            [
                'countries' => Countries::getNames(Locale::getDefault()),
                'invoice' => $invoice,
                'products' => $order->getStoreOrdersProducts(),
                'order' => $order,
                'customer' => $customerOrder->getCustomer(),
                'path' => $params->get('kernel.project_dir'),
                'embedded' => true,
                'embed' => $embed,
            ]
        );

        $pdf->generateFromHtml($html, $output);

        $data = [
            'from' => [
                'email' => $order->getStore()->getEmail() ?: $params->get('app.notifications.email_sender'),
                'name' => $order->getStore()->getName(),
            ],
            'to' => [
                'email' => $customerOrder->getCustomer()->getEmail(),
                'name' => $customerOrder->getCustomer()->getFirstName() . ' ' . $customerOrder->getCustomer()->getLastName(),
            ],
            'subject' => 'Invoice #' . $order->getStoreInvoice()->getNumber(),
            'body' => $html,
            'attachment' => $output,
        ];

        try {
            $mail->send($data);
        } catch (\Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->json([
            'success' => true,
            'message' => $translator->trans('message.success.text'),
        ]);
    }
}