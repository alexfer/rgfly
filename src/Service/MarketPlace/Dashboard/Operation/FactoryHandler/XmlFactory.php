<?php declare(strict_types=1);

namespace Inno\Service\MarketPlace\Dashboard\Operation\FactoryHandler;

use Inno\Service\MarketPlace\Dashboard\Operation\Interface\HandleFactoryInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class XmlFactory implements HandleFactoryInterface
{
    /**
     * @var \SimpleXMLElement
     */
    private \SimpleXMLElement $xml;

    /**
     * @var CacheManager
     */
    public CacheManager $cacheManager;

    /**
     * @var ParameterBagInterface
     */
    public ParameterBagInterface $params;

    /**
     * @param string $root
     * @throws \Exception
     */
    public function __construct(string $root = 'products')
    {
        $xml = sprintf('<?xml version="1.0" encoding="utf-8"?><%s/>', $root);
        $this->xml = new \SimpleXMLElement($xml);
    }

    /**
     * @param array|object $collection
     * @param array $option
     * @return string|bool
     */
    public function build(array|object $collection, array $option): string|bool
    {
        foreach ($collection as $item) {

            $createdAt = $item->getCreatedAt();

            $child = $this->xml->addChild('product');
            $child->addChild('id', (string)$item->getId());

            if (isset($option['name'])) {
                $child->addChild('name', str_replace('&', 'and', $item->getName()));
            }

            if (isset($option['short_name'])) {
                $child->addChild('shortName', str_replace('&', 'and', $item->getShortName()));
            }

            if (isset($option['price'])) {
                $child->addChild('price', number_format($item->getCost() + $item->getFee(), 2, '.', ' '));
            }

            if (isset($option['quantity'])) {
                $child->addChild('quantity', (string)$item->getQuantity());
            }

            if (isset($option['description'])) {
                $child->addChild('description', '<![CDATA[' . strip_tags($item->getDescription()) . ']]>');
            }

            if (isset($option['sku'])) {
                $child->addChild('sku', $item->getSku());
            }

            if (isset($option['fee'])) {
                $child->addChild('fee', (string)$item->getFee());
            }

            if (isset($option['cost'])) {
                $child->addChild('cost', (string)$item->getCost());
            }

            if (isset($option['pckg_discount'])) {
                $package = $child->addChild('package');
                $package->addChild('discount', (string)$item->getPckgDiscount());
                $package->addChild('quantity', (string)$item->getPckgQuantity());
            }

            if (isset($option['image'])) {
                $image = $child->addChild('image');
                $images = $item->getStoreProductAttaches();

                foreach ($images as $attach) {
                    $image->addAttribute('size', (string)$attach->getAttach()->getSize());
                    $image->addAttribute('mime', $attach->getAttach()->getMime());
                    $image->addChild('name', $attach->getAttach()->getName());
                    $url = sprintf('%s/%d', $this->params->get('product_storage_dir'), $item->getId());
                    $picture = $this->cacheManager->getBrowserPath(parse_url($url . '/' . $attach->getAttach()->getName(), PHP_URL_PATH), 'product_view', [], null);
                    $image->addChild('url', $picture);
                }
            }

            if (isset($option['category'])) {
                $category = $child->addChild('category', null);
                $categories = $item->getStoreCategoryProducts();

                foreach ($categories as $_category) {
                    $category->addChild('name', str_replace('&', 'and', $_category->getCategory()->getName()));
                    $category->addChild('parent', str_replace('&', 'and', $_category->getCategory()->getParent()->getName()));
                }
            }

            if (isset($option['supplier'])) {
                $supplier = $child->addChild('supplier');
                $supplier->addChild('name', $item->getStoreProductSupplier()?->getSupplier()?->getName());
                $supplier->addChild('country', $item->getStoreProductSupplier()?->getSupplier()?->getCountry());
            }

            if (isset($option['manufacturer'])) {
                $manufacturer = $child->addChild('manufacturer');
                $manufacturer->addChild('name', $item->getStoreProductManufacturer()?->getManufacturer()?->getName());
            }

            if (isset($option['brand'])) {
                $brand = $child->addChild('brand');
                $brand->addChild('name', $item->getStoreProductBrand()?->getBrand()?->getName());
            }

            if (isset($option['discount'])) {
                $discount = $child->addChild('discount');
                $discount->addChild('value', (string)$item->getStoreProductDiscount()->getValue());
                $discount->addChild('unit', $item->getStoreProductDiscount()->getUnit());
            }

            if (isset($option['created_at'])) {
                $child->addChild('createdAt', $createdAt->format('Y-m-d H:i:s'));
            }

        }

        return html_entity_decode($this->xml->asXML());
    }
}