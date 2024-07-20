<?php

namespace App\Service\MarketPlace\Store\Order;

use App\Entity\MarketPlace\{Store, StoreCustomer, StoreCustomerOrders, StoreOrders, StoreOrdersProduct, StoreProduct};
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Service\MarketPlace\Store\Order\Interface\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request, RequestStack};

final class Processor implements ProcessorInterface
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
        $session = $this->request->getSession();

        if (!$session->isStarted()) {
            $session->start();
        }

        $this->sessionId = $session->getId();
        $this->data = $this->request->toArray();
    }

    /**
     * @param int|null $id
     * @return StoreOrders|null
     */
    public function findOrder(int $id = null): ?StoreOrders
    {
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
     * @param array $input
     * @return void
     */
    public function updateQuantity(array $input): void
    {
        foreach ($input['order']['product'] as $key => $value) {
            $order = $this->findOrder($input['order'][$key]);
            $product = $this->em->getRepository(StoreOrdersProduct::class)
                ->findOneBy(['id' => $value, 'orders' => $order]);
            $product->setQuantity($input['order']['quantity'][$key]);
            $this->em->persist($product);
        }
        $this->em->flush();
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
            return $this->init($customer);
        }

        if ($this->existsProduct($order) === null) {
            $order = $this->process($order);
        }

        return $order;
    }

    /**
     * @return float|int
     */
    private function total(): float|int
    {
        $cost = round($this->getProduct()->getCost()) + round($this->getProduct()->getFee());
        $discount = intval($this->getProduct()->getDiscount());
        $total = ($cost - (($cost * $discount) - $discount) / 100);

        if (!$discount) {
            $total = $cost;
        }
        return $total;
    }

    /**
     * @param StoreCustomer|null $customer
     * @return StoreOrders
     */
    private function init(?StoreCustomer $customer): StoreOrders
    {
        $order = new StoreOrders();
        $order->setStore($this->store())
            ->setNumber(MarketPlaceHelper::orderNumber(6))
            ->setSession($this->sessionId)
            ->setTotal($this->total())
            ->setTax($this->store()->getTax())
            ->addStoreOrdersProduct($this->orderProduct())
            ->addStoreCustomerOrder($this->orderCustomer($customer));

        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    /**
     * @param StoreCustomer|null $customer
     * @return StoreCustomerOrders
     */
    private function orderCustomer(?StoreCustomer $customer): StoreCustomerOrders
    {
        $customerOrder = new StoreCustomerOrders();
        if ($customer->getId()) {
            $customerOrder->setCustomer($customer);
        }
        $this->em->persist($customerOrder);
        return $customerOrder;
    }

    /**
     * @return StoreOrdersProduct
     */
    private function orderProduct(): StoreOrdersProduct
    {
        $product = new StoreOrdersProduct();
        $product->setColor($this->data['color'] ?: null)
            ->setSize($this->data['size'] ?: null)
            ->setProduct($this->getProduct());
        $this->em->persist($product);
        return $product;
    }

    /**
     * @param StoreOrders $order
     * @return StoreOrders
     */
    private function process(StoreOrders $order): StoreOrders
    {
        $total = $order->getTotal() + $this->total();
        $order->setTotal($total)
            ->setSession($this->sessionId);
        $order->addStoreOrdersProduct($this->orderProduct());
        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    /**
     * @return Store
     */
    protected function store(): Store
    {
        return $this->getProduct()->getStore();
    }

    /**
     * @param StoreOrders $order
     * @return StoreOrdersProduct|null
     */
    protected function existsProduct(StoreOrders $order): ?StoreOrdersProduct
    {
        return $this->em->getRepository(StoreOrdersProduct::class)
            ->findOneBy([
                'orders' => $order,
                'product' => $this->getProduct(),
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
}