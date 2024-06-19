<?php

namespace App\Service\MarketPlace\Dashboard\Product;

use App\Entity\MarketPlace\{Store, StoreCategory, StoreCategoryProduct, StoreCoupon, StoreProduct};
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Service\MarketPlace\Dashboard\Product\Interface\ServeInterface as ProductServeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Serve extends Handle implements ProductServeInterface
{

    /**
     * @var UserInterface
     */
    protected UserInterface $user;

    /**
     * @var FormInterface
     */
    protected FormInterface $form;

    /**
     * @param UserInterface $user
     * @param FormInterface|null $form
     * @return FormInterface|null
     */
    public function handle(UserInterface $user, ?FormInterface $form = null): ?FormInterface
    {
        $this->user = $user;

        if ($form) {
            $this->form = $form;
            return $this->form->handleRequest($this->request);
        }
        return null;
    }

    /**
     * @param StoreProduct $product
     * @return StoreProduct
     * @throws \Exception
     */
    private function setDeleted(StoreProduct $product): StoreProduct
    {
        $date = new \DateTime('@' . strtotime('now'));
        $product->setDeletedAt($date);
        return $product;
    }

    /**
     * @param Store $store
     * @param StoreProduct $product
     * @return StoreProduct
     * @throws \Exception
     */
    public function update(Store $store, StoreProduct $product): StoreProduct
    {
        $this->repository->removeCategoryProduct($product);

        $this->category($product);

        if ($store->getDeletedAt()) {
            $product = $this->setDeleted($product);
        }

        $this->em->persist($store);
        $this->em->flush();

        $this->handleRelations($product);

        foreach ($product->getStoreProductAttributes() as $attribute) {
            $values = $attribute->getStoreProductAttributeValues();
            foreach ($values as $value) {
                $this->em->remove($value);
                $this->em->flush();
            }
        }
        $sku = $this->form->get('sku')->getData();
        if (!$sku) {
            $sku = $this->sku($product->getId());
        }

        $product->setSku($sku);
        $this->em->persist($product);
        $this->attributes($product);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    /**
     * @param Store $store
     * @param StoreProduct $product
     * @return StoreProduct
     * @throws \Exception
     */
    public function create(Store $store, StoreProduct $product): StoreProduct
    {
        $this->category($product);

        $product->setStore($store);
        $this->em->persist($product);

        if ($store->getDeletedAt()) {
            $product = $this->setDeleted($product);
        }

        $this->em->persist($store);
        $this->em->flush();

        $this->handleRelations($product);

        $product->setSlug(MarketPlaceHelper::slug($product->getId()));

        $sku = $this->form->get('sku')->getData();
        if (!$sku) {
            $sku = $this->sku($product->getId());
        }

        $product->setSku($sku);
        $this->em->persist($product);

        $this->attributes($product);

        $this->em->flush();

        return $product;
    }

    /**
     * @param int $id
     * @return string
     */
    private function sku(int $id): string
    {
        $sku = [
            'S' . $this->request->get('store'),
            'C' . $this->form->get('category')->getData(),
            'P' . $id,
            'N' . mb_substr($this->form->get('name')->getData(), 0, 4, 'utf8'),
            'C' . (int)$this->form->get('cost')->getData()
        ];

        return join('-', $sku);
    }

    /**
     * @param StoreProduct $product
     * @return mixed
     */
    protected function category(StoreProduct $product): mixed
    {

        $category = $this->categories->request($this->form);

        if ($category) {
            $productCategory = new StoreCategoryProduct();
            $productCategory->setProduct($product)
                ->setCategory($this->em->getRepository(StoreCategory::class)->find($category));
            $this->em->persist($productCategory);
        }

        return $category;
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