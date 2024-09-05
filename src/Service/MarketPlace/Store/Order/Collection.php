<?php

namespace App\Service\MarketPlace\Store\Order;

use App\Entity\MarketPlace\{StoreCustomer, StoreOrders};
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Service\MarketPlace\Store\Order\Interface\CollectionInterface;
use App\Storage\MarketPlace\FrontSessionHandler;
use App\Storage\MarketPlace\FrontSessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request, RequestStack};

final class Collection implements CollectionInterface
{

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var string|null
     */
    private ?string $sessionId;

    /**
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em
     * @param FrontSessionInterface $frontSession
     */
    public function __construct(
        protected RequestStack                  $requestStack,
        private readonly EntityManagerInterface $em,
        private readonly FrontSessionInterface  $frontSession,
    )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->sessionId = $this->request->getSession()->getId();

        if ($this->request->cookies->has(FrontSessionHandler::NAME)) {
            $this->sessionId = $this->request->cookies->get(FrontSessionHandler::NAME);
        }
    }

    /**
     * @param StoreCustomer|null $customer
     * @return array|null
     */
    public function getOrders(?StoreCustomer $customer = null): ?array
    {
        $orders = $this->em->getRepository(StoreOrders::class)
            ->collection($this->sessionId, $customer);

        $this->setFrontSession($orders);

        return $orders ?? null;
    }

    /**
     * @param array|null $orders
     * @return void
     */
    private function setFrontSession(?array $orders): void
    {
        $sessionOrders = [];
        if ($orders['summary']) {
            foreach ($orders['summary'] as $order) {
                $sessionOrders[$order['id']] = [
                    'store' => $order['store']['id'],
                    'tax' => $order['store']['tax'],
                    'number' => $order['number'],
                    'quantity' => $order['qty'],
                ];
            }
            $this->frontSession->set($this->sessionId, serialize($sessionOrders));
        }
    }

    /**
     * @return array|null
     */
    public function getOrderProducts(): ?array
    {
        $orders = $this->getOrders();

        if ($orders['summary'] === null) {
            return null;
        }

        $clientOrders = [];

        foreach ($orders['summary'] as $order) {
            $clientOrders[] = $order['id'];
        }

        return [
            'quantity' => $this->quantity(),
            'clientOrders' => $clientOrders,
        ];
    }

    /**
     * @return array|null
     */
    public function collection(): ?array
    {
        $orders = $this->getOrders();

        if ($orders['summary'] === null) {
            return null;
        }
        return $this->getCollection($orders);
    }

    /**
     * @param array|null $orders
     * @return array
     */
    protected function getCollection(?array $orders): array
    {
        $ClientOrders = $total = $fee = $products = [];
        $collection['quantity'] = $this->quantity();

        foreach ($orders['summary'] as $order) {
            $id = $order['id'];

            foreach ($order['products'] as $product) {
                $attach = $product['product']['attachment'];
                $price = MarketPlaceHelper::discount(
                    $product['product']['cost'],
                    $product['product']['reduce']['value'],
                    $product['product']['fee'],
                    $product['quantity'],
                    $product['product']['reduce']['unit']
                );

                $products[$id][$product['id']] = [
                    'id' => $product['product']['id'],
                    'short_name' => $product['product']['short_name'],
                    'name' => $product['product']['name'],
                    'slug' => $product['product']['slug'],
                    'order_id' => $id,
                    'cost' => $product['product']['cost'],
                    'price' => $price,
                    'reduce' => $product['product']['reduce'],
                    'fee' => $product['product']['fee'],
                    'quantity' => $product['quantity'],
                    'size' => $product['size'],
                    'color' => $product['color'],
                    'attach' => $attach,
                ];

                $total[$id][] = $price;
                $fee[$id][] = $product['product']['fee'];

            }
            $ClientOrders[$id] = [
                'id' => $id,
                'number' => $order['number'],
                'totalFee' => array_sum($fee[$id]),
                'total' => array_sum($total[$id]),
                'store' => [
                    'slug' => $order['store']['slug'],
                    'name' => $order['store']['name'],
                    'currency' => $order['store']['currency'],
                ],
                'products' => $products,
            ];
        }
        $collection['orders'] = $ClientOrders;
        return $collection;
    }

    private function quantity(): int
    {
        $quantity = [];
        $orders = $this->frontSession->get($this->sessionId);

        if ($orders) {
            $orders = unserialize($orders);

            foreach ($orders as $order) {
                $quantity[] = $order['quantity'];
            }
        }
        return array_sum($quantity);
    }

}