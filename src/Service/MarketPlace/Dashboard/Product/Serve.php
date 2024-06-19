<?php

namespace App\Service\MarketPlace\Dashboard\Product;

use App\Entity\MarketPlace\{Store, StoreCoupon, StoreProduct};
use App\Service\MarketPlace\Dashboard\Product\Interface\ServeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Serve extends Handle implements ServeInterface
{

    /**
     * @var UserInterface
     */
    protected UserInterface $user;

    /**
     * @param UserInterface $user
     * @return void
     */
    public function handle(UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @param Store $store
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array|null
     */
    public function index(Store $store, string $search = null, int $offset = 0, int $limit = 20): ?array
    {
        return $this->em->getRepository(StoreProduct::class)
            ->products($store, $search, $offset, $limit)['result'] ?? null;
    }

    /**
     * @param Store $store
     * @param string $type
     * @return array|null
     */
    public function coupon(Store $store, string $type): ?array
    {
        return $this->em->getRepository(StoreCoupon::class)
            ->fetchActive($store, $type);
    }

    /**
     * @param Store $store
     * @return string
     */
    public function currency(Store $store): string
    {
        return $store->getCurrency();
    }
}