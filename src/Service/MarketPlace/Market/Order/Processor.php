<?php

namespace App\Service\MarketPlace\Market\Order;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketCustomerOrders;
use App\Entity\MarketPlace\MarketOrders;
use App\Entity\MarketPlace\MarketOrdersProduct;
use App\Entity\MarketPlace\MarketProduct;
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Service\MarketPlace\Market\Order\Interface\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Processor implements ProcessorInterface
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
     * @var array|null
     */
    private ?array $data;

    /**
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em
     */
    public function __construct(
        protected RequestStack                  $requestStack,
        private readonly EntityManagerInterface $em,
    )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->data = $this->request->toArray();
    }

    /**
     * @param string|null $sessionId
     * @param int|null $id
     * @return MarketOrders|null
     */
    public function findOrder(?string $sessionId, int $id = null): ?MarketOrders
    {
        $this->sessionId = $sessionId;

        $condition = [
            'market' => $this->market(),
            'session' => $this->sessionId,
        ];

        if ($id) {
            unset($condition['market']);
            $condition['id'] = $id;
        }

        return $this->em->getRepository(MarketOrders::class)->findOneBy($condition);
    }

    /**
     * @param string|null $sessionId
     * @param array $input
     * @return void
     */
    public function updateQuantity(?string $sessionId, array $input): void
    {
        foreach ($input['order']['product'] as $key => $value) {
            $order = $this->findOrder($sessionId, $input['order'][$key]);
            $product = $this->em->getRepository(MarketOrdersProduct::class)
                ->findOneBy(['id' => $value, 'orders' => $order]);
            $product->setQuantity($input['order']['quantity'][$key]);
            $this->em->persist($product);
            $this->em->flush();
        }
    }

    /**
     * @param MarketOrders|null $order
     * @param MarketCustomer|null $customer
     * @return MarketOrders
     */
    public function processOrder(
        ?MarketOrders   $order,
        ?MarketCustomer $customer,
    ): MarketOrders
    {
        if (!$order) {
            $order = $this->setOrder($customer);
        }
        if ($order) {
            $product = $this->existsProduct();
            if (!$product) {
                $order = $this->updateOrder($order);
            } else {
                $order = $this->addProduct($order);
            }
        }
        return $order;
    }

    /**
     * @param MarketOrders|null $order
     * @return MarketOrders
     */
    private function addProduct(?MarketOrders $order): MarketOrders
    {
        if($this->existsOrderProduct($order)) {
            return $order;
        }
        $product = $this->getProduct();
        $orderProduct = new MarketOrdersProduct();
        $orderProduct->setOrders($order)
            ->setProduct($product)
            ->setQuantity(1)
            ->setColor($this->data['color'])
            ->setSize($this->data['size'])
            ->setDiscount($product->getDiscount())
            ->setCost($product->getCost());
        $this->em->persist($orderProduct);
        $order->setTotal($order->getTotal() + $product->getCost());
        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    /**
     * @param MarketCustomer|null $customer
     * @return MarketOrders
     */
    private function setOrder(?MarketCustomer $customer): MarketOrders
    {
        $order = new MarketOrders();
        $order->setMarket($this->market())
            ->setSession($this->sessionId)
            ->setTotal($this->getProduct()->getCost());

        $this->setCustomer($order, $customer);
        $this->em->persist($order);
        $this->setProduct($order);
        $this->flush();
        return $order;
    }

    /**
     * @param MarketOrders $order
     * @return MarketOrders
     */
    private function updateOrder(MarketOrders $order): MarketOrders
    {
        $order->setTotal($order->getTotal() + $this->getProduct()->getCost())
            ->setSession($this->sessionId);
        $this->em->persist($order);
        $this->setProduct($order, false);
        $this->em->flush();
        return $order;
    }

    /**
     * @param MarketOrders $order
     * @param bool $withNumber
     * @return void
     */
    private function setProduct(MarketOrders $order, bool $withNumber = true): void
    {
        $product = new MarketOrdersProduct();
        $product->setOrders($order)
            ->setColor($this->data['color'])
            ->setSize($this->data['size'])
            ->setProduct($this->getProduct())
            ->setCost($this->getProduct()->getCost())
            ->setDiscount($this->getProduct()->getDiscount());

        if ($withNumber) {
            $product->getOrders()
                ->setNumber(MarketPlaceHelper::orderNumber(6));
        }

        $this->em->persist($product);
    }

    /**
     * @param MarketOrders $order
     * @param MarketCustomer|null $customer
     * @return void
     */
    private function setCustomer(MarketOrders $order, ?MarketCustomer $customer): void
    {
        $customerOrder = new MarketCustomerOrders();
        $customerOrder->setOrders($order);
        if ($customer->getId()) {
            $customerOrder->setCustomer($customer);
        }
        $this->em->persist($customerOrder);
    }

    /**
     * @return Market
     */
    protected function market(): Market
    {
        return $this->getProduct()->getMarket();
    }

    /**
     * @return MarketOrdersProduct|null
     */
    protected function existsProduct(): ?MarketOrdersProduct
    {
        return $this->em->getRepository(MarketOrdersProduct::class)
            ->findOneBy([
                'product' => $this->getProduct(),
                'size' => $this->data['size'] ?: null,
                'color' => $this->data['color'] ?: null,
            ]);
    }

    /**
     * @param MarketOrders $order
     * @return MarketOrdersProduct|null
     */
    protected function existsOrderProduct(MarketOrders $order): ?MarketOrdersProduct
    {
        return $this->em->getRepository(MarketOrdersProduct::class)
            ->findOneBy([
                'product' => $this->getProduct(),
                'orders' => $order,
                'size' => $this->data['size'] ?: null,
                'color' => $this->data['color'] ?: null,
            ]);
    }

    /**
     * @return MarketProduct|null
     */
    protected function getProduct(): ?MarketProduct
    {
        return $this->em->getRepository(MarketProduct::class)
            ->findOneBy([
                'slug' => $this->request->get('product'),
            ]);
    }

    /**
     * @return void
     */
    private function flush(): void
    {
        $this->em->flush();
    }
}