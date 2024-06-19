<?php

namespace App\Service\MarketPlace\Dashboard\Product;

use App\Entity\MarketPlace\{StoreBrand,
    StoreCategoryProduct,
    StoreManufacturer,
    StoreProduct,
    StoreProductAttribute,
    StoreProductAttributeValue,
    StoreProductBrand,
    StoreProductManufacturer,
    StoreProductSupplier,
    StoreSupplier};
use App\Helper\MarketPlace\MarketAttributeValues;
use App\Service\MarketPlace\Dashboard\Category\Interface\ServeInterface as CategoryServeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\{Request, RequestStack};

abstract class Handle
{

    /**
     * @var Request|null
     */
    protected ?Request $request;

    /**
     * @var StoreCategoryProduct|EntityRepository
     */
    protected StoreCategoryProduct|EntityRepository $repository;

    /**
     * @var CategoryServeInterface
     */
    protected CategoryServeInterface $categories;

    /**
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     * @param CategoryServeInterface $categories
     */
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected RequestStack                    $requestStack,
        CategoryServeInterface                    $categories
    )
    {
        $this->request = $this->requestStack->getCurrentRequest();
        $this->categories = $categories;
        $this->repository = $this->categories->categoryProductRepository();
    }

    /**
     * @param StoreProduct $product
     * @return $this
     */
    protected function attributes(StoreProduct $product): self
    {
        $attributes['colors'] = $this->form->get('color')->getData();
        $attributes['size'] = $this->form->get('size')->getData();

        if ($attributes['colors']) {
            $attribute = new StoreProductAttribute();
            $attribute->setProduct($product)->setName('color')->setInFront(1);
            $attributeColors = array_flip(MarketAttributeValues::ATTRIBUTES['Color']);

            foreach ($attributes['colors'] as $color) {
                $attributeValue = new StoreProductAttributeValue();
                $value = $attributeValue->setAttribute($attribute)->setValue($attributeColors[$color])->setExtra([$color]);
                $this->em->persist($value);
            }
            $this->em->persist($attribute);
        }

        if ($attributes['size']) {
            $attribute = new StoreProductAttribute();
            $attribute->setProduct($product)->setName('size')->setInFront(1);

            foreach ($attributes['size'] as $size) {
                $attributeValue = new StoreProductAttributeValue();
                $value = $attributeValue->setAttribute($attribute)->setValue($size)->setExtra([$size]);
                $this->em->persist($value);
            }
            $this->em->persist($attribute);
        }
        return $this;
    }

    /**
     * @param StoreProduct $product
     * @return $this
     */
    protected function handleRelations(StoreProduct $product): self
    {
        $supplier = $this->em->getRepository(StoreSupplier::class)
            ->findOneBy(['id' => $this->form->get('supplier')->getData()]);
        $brand = $this->em->getRepository(StoreBrand::class)
            ->findOneBy(['id' => $this->form->get('brand')->getData()]);
        $manufacturer = $this->em->getRepository(StoreManufacturer::class)
            ->findOneBy(['id' => $this->form->get('manufacturer')->getData()]);

        if ($supplier) {
            $ps = $product->getStoreProductSupplier();
            if (!$ps) {
                $ps = new StoreProductSupplier();
            }
            $ps->setProduct($product)->setSupplier($supplier);
            $this->em->persist($ps);
        } else {
            $ps = $product->getStoreProductSupplier();
            if ($ps) {
                $ps->setProduct(null)->setSupplier(null);
                $this->em->persist($ps);
                $this->em->flush();
                $this->em->getRepository(StoreProductSupplier::class)->drop($ps->getId());
            }
        }

        if ($brand) {
            $pb = $product->getStoreProductBrand();
            if (!$pb) {
                $pb = new StoreProductBrand();
            }
            $pb->setProduct($product)->setBrand($brand);
            $this->em->persist($pb);
        } else {
            $pb = $product->getStoreProductBrand();
            if ($pb) {
                $pb->setProduct(null)->setBrand(null);
                $this->em->persist($pb);
                $this->em->flush();
                $this->em->getRepository(StoreProductBrand::class)->drop($pb->getId());
            }
        }

        if ($manufacturer) {
            $pm = $product->getStoreProductManufacturer();
            if (!$pm) {
                $pm = new StoreProductManufacturer();
            }
            $pm->setProduct($product)->setManufacturer($manufacturer);
            $this->em->persist($pm);
        } else {
            $pm = $product->getStoreProductManufacturer();
            if ($pm) {
                $pm->setProduct(null)->setManufacturer(null);
                $this->em->persist($pm);
                $this->em->flush();
                $this->em->getRepository(StoreProductManufacturer::class)->drop($pm->getId());
            }
        }

        return $this;
    }

}