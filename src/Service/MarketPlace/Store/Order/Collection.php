<?php

namespace App\Service\MarketPlace\Store\Order;

use App\Entity\MarketPlace\StoreOrders;
use App\Service\MarketPlace\Store\Order\Interface\CollectionInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class Collection implements CollectionInterface
{

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private EntityManagerInterface $em,
    )
    {

    }

    /**
     * @param string|null $sessId
     * @return array|null
     */
    public function getOrders(?string $sessId = null): ?array
    {
        return $this->em->getRepository(StoreOrders::class)
            ->findBy(['session' => $sessId]);
    }

    public function getOrderProducts(?string $sessId = null): ?int
    {
        $orders = $this->getOrders($sessId);
        $result = [];
        foreach ($orders as $order) {
            foreach ($order->getStoreOrdersProducts() as $product) {
                $result[] = $product->getId();
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
        return $this->getCollection($orders);
    }

    /**
     * @param array|null $orders
     * @return array
     */
    protected function getCollection(?array $orders): array
    {
        $collection = $total = $fee = $products = [];

        foreach ($orders as $order) {
            $storeProducts = $order->getStoreOrdersProducts()->toArray();

            foreach ($storeProducts as $product) {
                $attach = [];

                foreach ($product->getProduct()->getStoreProductAttaches() as $storeProductAttach) {
                    $attachment = $storeProductAttach->getAttach();
                    $attach[$attachment->getId()] = $attachment->getName();
                }

                $products[$order->getId()][$product->getId()] = [
                    'id' => $product->getProduct()->getId(),
                    'short_name' => $product->getProduct()->getShortName(),
                    'name' => $product->getProduct()->getName(),
                    'slug' => $product->getProduct()->getSlug(),
                    'order_id' => $order->getId(),
                    'cost' => $product->getCost(),
                    'fee' => $product->getProduct()->getFee(),
                    'percent' => $product->getDiscount(),
                    'quantity' => $product->getQuantity(),
                    'size' => $product->getSize(),
                    'color' => $product->getColor(),
                    'attach' => reset($attach),
                ];

                $cost = $product->getCost() * $product->getQuantity();
                $total[$order->getId()][] = $cost - ($cost * $product->getDiscount() - $product->getDiscount()) / 100;
                $fee[$order->getId()][] = $product->getProduct()->getFee();

            }
            $collection[$order->getId()] = [
                'id' => $order->getId(),
                'number' => $order->getNumber(),
                'total' => array_sum($total[$order->getId()]) + array_sum($fee[$order->getId()]),
                'store' => [
                    'slug' => $order->getStore()->getSlug(),
                    'name' => $order->getStore()->getName(),
                    'currency' => $order->getStore()->getCurrency(),
                ],
                'products' => $products,
            ];
        }
        return $collection;
    }

}