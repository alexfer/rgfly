<?php

namespace App\Service\MarketPlace\Store\Order;

use App\Entity\MarketPlace\StoreOrders;
use App\Entity\MarketPlace\StoreOrdersProduct;
use App\Service\MarketPlace\Store\Order\Interface\ComputeInterface;
use Doctrine\ORM\EntityManagerInterface;

class Compute implements ComputeInterface
{

    /**
     * @var array
     */
    private array $orders;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private readonly EntityManagerInterface $em)
    {
        $this->orders = [];
    }

    /**
     * @param array $input
     * @return void
     */
    public function process(array $input): void
    {
        foreach ($input['order']['quantity'] as $key => $value) {
            $product = $this->orderProduct($key);
            $product->setQuantity($value);
            $this->em->persist($product);
            $cost = $product->getProduct()->getCost() + $product->getProduct()->getFee();
            $this->orders[$product->getOrders()->getId()][$product->getId()] = [
                'amount' => ($cost - (($cost * $product->getProduct()->getDiscount()) - $product->getProduct()->getDiscount()) / 100) * $value,
            ];
        }
        $this->em->flush();
        $this->updateAmount();
    }

    /**
     * @param int $id
     * @return StoreOrdersProduct
     */
    private function orderProduct(int $id): StoreOrdersProduct
    {
        return $this->em->getRepository(StoreOrdersProduct::class)
            ->findOneBy(['id' => $id]);
    }

    /**
     * @return void
     */
    private function updateAmount(): void
    {
        $amount = [];
        foreach ($this->orders as $order => $products) {
            foreach ($products as $product) {
                $amount[$order][] = $product['amount'];
            }
            $amount[$order] = array_sum($amount[$order]);
            $this->order($order, $amount[$order]);
        }
    }

    /**
     * @param int $id
     * @param mixed $amount
     * @return void
     */
    private function order(int $id, mixed $amount): void
    {
        $order = $this->em->getRepository(StoreOrders::class)->find($id);
        $order->setTotal($amount);
        $this->em->persist($order);
        $this->em->flush();
    }
}