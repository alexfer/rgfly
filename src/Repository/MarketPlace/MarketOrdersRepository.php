<?php

namespace App\Repository\MarketPlace;

use App\Entity\MarketPlace\MarketOrders;
use App\Service\MarketPlace\Currency;
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
                $products[$order->getId()][$product->getId()] = [
                    'short_name' => $product->getProduct()->getShortName(),
                    'name' => $product->getProduct()->getName(),
                    'slug' => $product->getProduct()->getSlug(),
                    'order_id' => $order->getId(),
                    'cost' => $product->getProduct()->getCost(),
                    'discount' => $product->getProduct()->getDiscount(),
                    'size' => $product->getSize(),
                    'color' => $product->getColor(),
                ];
            }
            $collection[$order->getId()] = [
                'id' => $order->getId(),
                'number' => $order->getNumber(),
                'total' => $order->getTotal(),
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
