<?php declare(strict_types=1);

namespace App\Service\MarketPlace\Store\Order;

use App\Storage\MarketPlace\FrontSessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class ShopService
{
    /**
     * @param RequestStack $requestStack
     * @param FrontSessionInterface $frontSession
     */
    public function __construct(
        private RequestStack          $requestStack,
        private FrontSessionInterface $frontSession
    )
    {
    }

    /**
     * @return int
     */
    public function quantity(): int
    {
        $quantity = [];

        $orders = $this->frontSession->get(
            (string)$this->requestStack->getCurrentRequest()->cookies?->get('rgfly')
        );

        if ($orders) {
            $orders = unserialize($orders);

            foreach ($orders as $order) {
                $quantity[] = $order['quantity'];
            }
        }
        return array_sum($quantity);
    }
}