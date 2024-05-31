<?php

namespace App\Service\MarketPlace\Store\Order;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreCustomerOrders;
use App\Entity\MarketPlace\StoreOrders;
use App\Entity\MarketPlace\StoreOrdersProduct;
use App\Entity\MarketPlace\StoreProduct;
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Service\MarketPlace\Store\Order\Interface\ProcessorInterface;
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
     * @return StoreOrders|null
     */
    public function findOrder(?string $sessionId, int $id = null): ?StoreOrders
    {
        $this->sessionId = $sessionId;

        $condition = [
            'store' => $this->store(),
            'session' => $this->sessionId,
        ];

        if ($id) {
            unset($condition['store']);
            $condition['id'] = $id;
        }

        return $this->em->getRepository(StoreOrders::class)->findOneBy($condition);
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
            $product = $this->em->getRepository(StoreOrdersProduct::class)
                ->findOneBy(['id' => $value, 'orders' => $order]);
            $product->setQuantity($input['order']['quantity'][$key]);
            $this->em->persist($product);
            $this->em->flush();
        }
    }

    /**
     * @param StoreOrders|null $order
     * @param StoreCustomer|null $customer
     * @return StoreOrders
     */
    public function processOrder(
        ?StoreOrders   $order,
        ?StoreCustomer $customer,
    ): StoreOrders
    {
        if (!$order) {
            $order = $this->setOrder($customer);
        }

        if ($order) {
            if (!$this->existsProduct()) {
                $order = $this->updateOrder($order);
            }

            if (!$this->existsOrderProduct($order)) {
                $this->setProduct($order, false);
            }
        }
        return $order;
    }

    /**
     * @param StoreCustomer|null $customer
     * @return StoreOrders
     */
    private function setOrder(?StoreCustomer $customer): StoreOrders
    {
        $order = new StoreOrders();
        $order->setStore($this->store())
            ->setSession($this->sessionId)
            ->setTotal($this->getProduct()->getCost());

        $this->setCustomer($order, $customer);
        $this->em->persist($order);
        $this->setProduct($order);
        $this->flush();
        return $order;
    }

    /**
     * @param StoreOrders $order
     * @return StoreOrders
     */
    private function updateOrder(StoreOrders $order): StoreOrders
    {
        $order->setTotal($order->getTotal() + $this->getProduct()->getCost())
            ->setSession($this->sessionId);
        $this->em->persist($order);
        $this->setProduct($order, false);
        $this->em->flush();
        return $order;
    }

    /**
     * @param StoreOrders $order
     * @param bool $withNumber
     * @return void
     */
    private function setProduct(StoreOrders $order, bool $withNumber = true): void
    {
        $product = new StoreOrdersProduct();
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
     * @param StoreOrders $order
     * @param StoreCustomer|null $customer
     * @return void
     */
    private function setCustomer(StoreOrders $order, ?StoreCustomer $customer): void
    {
        $customerOrder = new StoreCustomerOrders();
        $customerOrder->setOrders($order);
        if ($customer->getId()) {
            $customerOrder->setCustomer($customer);
        }
        $this->em->persist($customerOrder);
    }

    /**
     * @return Store
     */
    protected function store(): Store
    {
        return $this->getProduct()->getStore();
    }

    /**
     * @return StoreOrdersProduct|null
     */
    protected function existsProduct(): ?StoreOrdersProduct
    {
        return $this->em->getRepository(StoreOrdersProduct::class)
            ->findOneBy([
                'product' => $this->getProduct(),
                'size' => $this->data['size'] ?: null,
                'color' => $this->data['color'] ?: null,
            ]);
    }

    /**
     * @param StoreOrders $order
     * @return StoreOrdersProduct|null
     */
    protected function existsOrderProduct(StoreOrders $order): ?StoreOrdersProduct
    {
        return $this->em->getRepository(StoreOrdersProduct::class)
            ->findOneBy([
                'product' => $this->getProduct(),
                'orders' => $order,
                'size' => $this->data['size'] ?: null,
                'color' => $this->data['color'] ?: null,
            ]);
    }

    /**
     * @return StoreProduct|null
     */
    protected function getProduct(): ?StoreProduct
    {
        return $this->em->getRepository(StoreProduct::class)
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