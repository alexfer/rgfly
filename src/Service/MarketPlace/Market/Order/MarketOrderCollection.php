<?php

namespace App\Service\MarketPlace\Market\Order;

use App\Entity\MarketPlace\MarketOrders;
use App\Service\MarketPlace\Market\Order\Interface\MarketOrderCollectionInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class MarketOrderCollection implements MarketOrderCollectionInterface
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
        return $this->em->getRepository(MarketOrders::class)
            ->findBy(['session' => $sessId]);
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
            $marketProducts = $order->getMarketOrdersProducts()->toArray();

            foreach ($marketProducts as $product) {
                $attach = [];

                foreach ($product->getProduct()->getMarketProductAttaches() as $marketProductAttach) {
                    $attachment = $marketProductAttach->getAttach();
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
                'market' => [
                    'slug' => $order->getMarket()->getSlug(),
                    'name' => $order->getMarket()->getName(),
                    'currency' => $order->getMarket()->getCurrency(),
                ],
                'products' => $products,
            ];
        }
        return $collection;
    }

}