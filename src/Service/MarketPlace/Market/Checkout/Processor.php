<?php

namespace App\Service\MarketPlace\Market\Checkout;

use App\Entity\MarketPlace\{MarketInvoice, MarketOrders, MarketPaymentGateway, MarketProduct};
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Service\MarketPlace\Market\Checkout\Interface\ProcessorInterface;
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
     * @var MarketOrders|null
     */
    protected ?MarketOrders $order;

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
    }

    /**
     * @param string $sessionId
     * @return MarketOrders|null
     */
    public function findOrder(string $sessionId): ?MarketOrders
    {
        $this->sessionId = $sessionId;
        $order = $this->em->getRepository(MarketOrders::class)->findOneBy([
            'number' => $this->request->get('order'),
            'session' => $this->sessionId,
            'status' => MarketOrders::STATUS['processing'],
        ]);

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
        return $this->order->getMarketOrdersProducts();
    }

    public function updateProducts(): void
    {
        foreach ($this->getProducts() as $product) {
            $item = $this->em->getRepository(MarketProduct::class)->find($product->getProduct());
            $item->setQuantity($item->getQuantity() - $product->getQuantity())
                ->setUpdatedAt(new \DateTimeImmutable());
            $this->em->persist($item);
        }
    }

    /**
     * @return MarketPaymentGateway
     */
    protected function getPaymentGateway(): MarketPaymentGateway
    {
        return $this->em->getRepository(MarketPaymentGateway::class)->findOneBy([
            'slug' => key($this->request->request->all('gateway')),
        ]);
    }

    /**
     * @param MarketInvoice $invoice
     * @return void
     */
    public function addInvoice(MarketInvoice $invoice): void
    {
        $invoice->setOrders($this->order)
            ->setPaymentGateway($this->getPaymentGateway())
            ->setNumber(MarketPlaceHelper::slug($this->order->getId(), 6, 'i'))
            ->setAmount($this->order->getTotal())
            ->setTax(0);

        $this->em->persist($invoice);
    }

    public function updateOrder(): void
    {
        $order = $this->order->setSession(null)->setStatus(MarketOrders::STATUS['confirmed']);
        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * @return int
     */
    public function countOrders(): int
    {
        $orders = $this->em->getRepository(MarketOrders::class)->findBy(['session' => $this->sessionId]);
        return count($orders);
    }

    /**
     * @return array
     */
    public function sum(): array
    {
        $sum = [];
        foreach ($this->getProducts() as $product) {
            $sum['itemSubtotal'][] = $product->getCost() - ((($product->getCost() * $product->getQuantity()) * $product->getDiscount()) - $product->getDiscount()) / 100;
            $sum['fee'][] = $product->getProduct()->getFee();
        }

        return $sum;
    }
}