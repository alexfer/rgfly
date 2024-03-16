<?php

namespace App\Service\MarketPlace\Market\Order;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketCustomerOrders;
use App\Entity\MarketPlace\MarketOrders;
use App\Entity\MarketPlace\MarketOrdersProduct;
use App\Service\MarketPlace\Market\Order\Interface\MarketOrderProductInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class MarketOrderProductProcessor implements MarketOrderProductInterface
{

    private readonly array $payload;

    /**
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em
     */
    public function __construct(
        protected RequestStack                  $requestStack,
        private readonly EntityManagerInterface $em,
    )
    {
        $request = $requestStack->getCurrentRequest();
        $this->payload = $request->getPayload()->all();
    }

    /**
     * @param MarketCustomer|null $customer
     * @return void
     */
    public function process(?MarketCustomer $customer): void
    {
        $this->deleteProduct();

        $products = $this->getProducts();
        $order = $this->getOrder();

        if (count($products) == 1) {
            $customerOrder = $this->getCustomerOrder($customer);

            if ($customer->getId() !== null) {
                $this->getOrder()->removeMarketCustomerOrder($customerOrder);
            }

            $this->em->remove($customerOrder);
            $this->em->remove($order);
        } else {
            $rewind = $order->getTotal() - ($this->getProduct()->getCost() * $this->getProduct()->getQuantity());
            $order->setTotal($rewind);
            $this->em->persist($order);
        }
        $this->flush();
    }

    /**
     * @return void
     */
    private function flush(): void
    {
        $this->em->flush();
    }


    /**
     * @return void
     */
    private function deleteProduct(): void
    {
        $product = $this->getProduct();
        $this->getOrder()->removeMarketOrdersProduct($product);
        $this->em->remove($product);
    }

    /**
     * @return EntityRepository
     */
    private function getOrderProductRepository(): EntityRepository
    {
        return $this->em->getRepository(MarketOrdersProduct::class);
    }

    /**
     * @return array
     */
    private function getProducts(): array
    {
        return $this->getOrderProductRepository()->findBy(['orders' => $this->getOrder()]);
    }

    /**
     * @return MarketOrdersProduct
     */
    private function getProduct(): MarketOrdersProduct
    {
        return $this->getOrderProductRepository()->find($this->payload['product']);
    }

    /**
     * @return MarketOrders|null
     */
    public function getOrder(): ?MarketOrders
    {
        return $this->em->getRepository(MarketOrders::class)->findOneBy([
            'session' => $this->payload['order'],
            'market' => $this->getMarket(),
        ]);
    }

    /**
     * @return Market
     */
    public function getMarket(): Market
    {
        return $this->em->getRepository(Market::class)->find($this->payload['market']);
    }

    /**
     * @param MarketCustomer|null $customer
     * @return MarketCustomerOrders|null
     */
    private function getCustomerOrder(?MarketCustomer $customer): ?MarketCustomerOrders
    {
        $condition = [
            'orders' => $this->getOrder(),
            'customer' => $customer,
        ];
        if ($customer->getId() === null) {
            unset($condition['customer']);
        }

        return $this->em->getRepository(MarketCustomerOrders::class)->findOneBy($condition);
    }
}