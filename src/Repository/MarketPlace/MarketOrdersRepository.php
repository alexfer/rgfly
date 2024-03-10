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
        $collection = $totalWithDiscount = $products = [];

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
                    'percent' => $product->getDiscount(),
                    'quantity' => $product->getQuantity(),
                    'size' => $product->getSize(),
                    'color' => $product->getColor(),
                    'attach' => reset($attach),
                ];
                //(cost - ((cost * product.quantity * percent) - percent)/100)
                $totalWithDiscount[$order->getId()][] = $product->getCost() - ((($product->getCost() * $product->getQuantity()) * $product->getDiscount()) - $product->getDiscount()) / 100;

            }
            $collection[$order->getId()] = [
                'id' => $order->getId(),
                'number' => $order->getNumber(),
                'total' => $order->getTotal(),
                'totalWithDiscount' => array_sum($totalWithDiscount[$order->getId()]),
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
