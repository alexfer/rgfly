<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketOrders>
 *
 * @method MarketOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketOrders[]    findAll()
 * @method MarketOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketOrdersRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketOrders::class);
    }

    /**
     * @param array $orders
     * @return array
     */
    public function getSerializedData(array $orders): array
    {
        $collection = $products = [];

        foreach ($orders as $order) {
            foreach ($order->getMarketOrdersProducts()->toArray() as $product) {

                $discount = 0;
                if ($product->getProduct()->getDiscount()) {
                    $discount = ($product->getProduct()->getCost() - (($product->getProduct()->getCost() * $product->getProduct()->getDiscount()) - $product->getProduct()->getDiscount()) / 100);
                }

                $products[$order->getId()][$product->getId()] = [
                    'id' => $product->getId(),
                    'short_name' => $product->getProduct()->getShortName(),
                    'name' => $product->getProduct()->getName(),
                    'slug' => $product->getProduct()->getSlug(),
                    'order_id' => $order->getId(),
                    'cost' => $product->getProduct()->getCost(),
                    'percent' => $product->getProduct()->getDiscount(),
                    'discount' => round($discount, 2),
                    'size' => $product->getSize(),
                    'color' => $product->getColor(),
                ];
            }
            $collection[$order->getId()] = [
                'id' => $order->getId(),
                'number' => $order->getNumber(),
                'total' => $order->getTotal(),
                'discount' => $order->getDiscount(),
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
