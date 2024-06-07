<?php

namespace App\Service\MarketPlace\Store\Order;

use App\Entity\MarketPlace\StoreOrders;
use App\Service\MarketPlace\Store\Order\Interface\CollectionInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class Collection implements CollectionInterface
{

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {

    }

    /**
     * @param string|null $sessId
     * @return array|null
     */
    public function getOrders(?string $sessId = null): ?array
    {
        return $this->em->getRepository(StoreOrders::class)
            ->collection($sessId);
    }

    /**
     * @param string|null $sessId
     * @return int|null
     */
    public function getOrderProducts(?string $sessId = null): ?int
    {
        $orders = $this->getOrders($sessId);
        if ($orders['summary'] === null) {
            return null;
        }
        $result = [];
        foreach ($orders['summary'] as $order) {
            foreach ($order['products'] as $product) {
                $result[] = $product['id'];
            }
        }
        return count($result);
    }

    /**
     * @param string|null $sessionId
     * @return array|null
     */
    public function collection(?string $sessionId): ?array
    {
        $orders = $this->getOrders($sessionId);
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
                    'cost' => $product['cost'],
                    'fee' => $product['product']['fee'],
                    'percent' => $product['product']['discount'],
                    'quantity' => $product['quantity'],
                    'size' => $product['size'],
                    'color' => $product['color'],
                    'attach' => $attach,
                ];

                $cost = $product['cost'] + $product['product']['fee'];
                $total[$id][] = $cost - ($cost * $product['discount'] - $product['discount']) / 100;
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