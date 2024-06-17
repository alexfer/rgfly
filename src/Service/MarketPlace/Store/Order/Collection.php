<?php

namespace App\Service\MarketPlace\Store\Order;

use App\Entity\MarketPlace\{StoreCustomer, StoreOrders};
use App\Service\MarketPlace\Store\Order\Interface\CollectionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request, RequestStack};

final readonly class Collection implements CollectionInterface
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
     */
    public function __construct(
        protected RequestStack         $requestStack,
        private EntityManagerInterface $em
    )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->sessionId = $this->request->getSession()->getId();
    }

    /**
     * @param StoreCustomer|null $customer
     * @return array|null
     */
    public function getOrders(?StoreCustomer $customer = null): ?array
    {
        return $this->em->getRepository(StoreOrders::class)
            ->collection($this->sessionId, $customer);
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
        $result = $summary = [];
        foreach ($orders['summary'] as $order) {
            foreach ($order['products'] as $product) {
                $result[] = $product['id'];
            }
            $summary[] = [
                'id' => $order['id'],
                'number' => $order['number'],
                'session' => $order['session'],
            ];
        }
        return [
            'count' => count($result),
            'orders' => $summary,
        ];
    }

    /**
     * @return array|null
     */
    public function collection(array $payload = null): ?array
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
        $collection = $total = $fee = $products = [];

        foreach ($orders['summary'] as $order) {
            $id = $order['id'];

            foreach ($order['products'] as $product) {
                $attach = $product['product']['attachment'];

                $products[$id][$product['id']] = [
                    'id' => $product['product']['id'],
                    'short_name' => $product['product']['short_name'],
                    'name' => $product['product']['name'],
                    'slug' => $product['product']['slug'],
                    'order_id' => $id,
                    'cost' => $product['product']['cost'],
                    'fee' => $product['product']['fee'],
                    'percent' => $product['product']['discount'],
                    'quantity' => $product['quantity'],
                    'size' => $product['size'],
                    'color' => $product['color'],
                    'attach' => $attach,
                ];

                $cost = $product['product']['cost'] + $product['product']['fee'];
                $discount = intval($product['product']['discount']);
                $total[$id][] = round($cost - ($cost * $discount - $discount) / 100, 2);
                $fee[$id][] = $product['product']['fee'];

            }
            $collection[$id] = [
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
        return $collection;
    }

}