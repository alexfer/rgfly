<?php

namespace App\Service\MarketPlace\Dashboard\Product\Interface;

use App\Entity\MarketPlace\{Store, StoreProduct};
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ServeInterface
{
    /**
     * @param UserInterface $user
     * @param FormInterface|null $form
     * @return FormInterface|null
     */
    public function handle(UserInterface $user, ?FormInterface $form = null): ?FormInterface;

    /**
     * @param Store $store
     * @param string|null $search
     * @param int $offset
     * @param int $limit
     * @return array|null
     */
    public function index(Store $store, string $search = null, int $offset = 0, int $limit = 20): ?array;

    /**
     * @param Store $store
     * @param string $type
     * @return array|null
     */
    public function coupon(Store $store, string $type): ?array;

    /**
     * @param Store $store
     * @return string
     */
    public function currency(Store $store): string;

    /**
     * @param Store $store
     * @param StoreProduct $product
     * @return StoreProduct
     */
    public function create(Store $store, StoreProduct $product): StoreProduct;

    /**
     * @param Store $store
     * @param StoreProduct $product
     * @return StoreProduct
     */
    public function update(Store $store, StoreProduct $product): StoreProduct;
}