<?php

namespace App\Service\MarketPlace\Store\Checkout;

use App\Entity\MarketPlace\{StoreCustomer, StoreInvoice, StoreOrders, StorePaymentGateway, StoreProduct};
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Service\MarketPlace\Store\Checkout\Interface\ProcessorInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request, RequestStack, Response};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Processor implements ProcessorInterface
{

    /**
     * @var Request|null
     */
    protected ?Request $request;

    /**
     * @var StoreOrders|null
     */
    protected ?StoreOrders $order;

    /**
     * @var string
     */
    private string $sessionId;

    /**
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     */
    public function __construct(
        private readonly EntityManagerInterface $em,
        readonly RequestStack                   $requestStack,
        private readonly TranslatorInterface    $translator,
        private readonly RouterInterface        $router,
    )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->sessionId = $this->request->getSession()->getId();
    }


    /**
     * @param string|null $status
     * @param StoreCustomer|null $customer
     * @return StoreOrders|null
     */
    public function findOrder(?string $status = StoreOrders::STATUS['processing'], ?StoreCustomer $customer = null): ?StoreOrders
    {
        $criteria = [
            'number' => $this->request->get('order'),
            'session' => $this->sessionId,
            'status' => $status,
        ];

        if ($customer) {
            unset($criteria['session']);
        }

        $order = $this->em->getRepository(StoreOrders::class)->findOneBy($criteria);

        if (!$order) {
            $message = $this->translator->trans('http_error_404.suggestion', ['url' => $this->router->generate('app_market_place_index')]);
            throw new NotFoundHttpException($message, null, Response::HTTP_NOT_FOUND);
        }

        $this->order = $order;

        return $order;
    }

    /**
     * @return Collection
     */
    private function getProducts(): Collection
    {
        return $this->order->getStoreOrdersProducts();
    }

    /**
     * @return void
     */
    protected function updateProducts(): void
    {
        foreach ($this->getProducts() as $product) {
            $item = $this->em->getRepository(StoreProduct::class)->find($product->getProduct());
            $item->setQuantity($item->getQuantity() - $product->getQuantity())
                ->setUpdatedAt(new \DateTimeImmutable());
            $this->em->persist($item);
        }
    }

    /**
     * @return StorePaymentGateway
     */
    private function getPaymentGateway(): StorePaymentGateway
    {
        return $this->em->getRepository(StorePaymentGateway::class)->findOneBy([
            'slug' => key($this->request->request->all('gateway')),
        ]);
    }

    /**
     * @param StoreInvoice $invoice
     * @param float $tax
     * @return void
     */
    public function addInvoice(StoreInvoice $invoice, float $tax = 0): void
    {
        $invoice->setOrders($this->order)
            ->setPaymentGateway($this->getPaymentGateway())
            ->setNumber(MarketPlaceHelper::slug($this->order->getId(), 6, 'i'))
            ->setAmount($this->order->getTotal())
            ->setTax($tax);

        $this->em->persist($invoice);
    }

    /**
     * @param string|null $status
     * @return void
     */
    public function updateOrder(?string $status = StoreOrders::STATUS['confirmed']): void
    {
        $order = $this->order->setSession(null)->setStatus($status);
        $this->em->persist($order);
        $this->updateProducts();
        $this->em->flush();
    }

    /**
     * @return int
     */
    public function countOrders(): int
    {
        $orders = $this->em->getRepository(StoreOrders::class)->findBy(['session' => $this->sessionId]);
        return count($orders);
    }

    /**
     * @return array
     */
    public function sum(): array
    {
        $sum = [];
        foreach ($this->getProducts() as $product) {
            $price = MarketPlaceHelper::discount(
                $product->getProduct()->getCost(),
                $product->getProduct()->getStoreProductDiscount()->getValue(),
                $product->getProduct()->getFee(),
                $product->getQuantity(),
                $product->getProduct()->getStoreProductDiscount()->getUnit()
            );
            $sum['itemSubtotal'][] = $price;
        }

        return $sum;
    }
}