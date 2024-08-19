<?php

declare(strict_types=1);

namespace App\Service\MarketPlace\Dashboard\Operation;

use App\Entity\MarketPlace\Enum\EnumOperation;
use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreOperation;
use App\Service\MarketPlace\Dashboard\Operation\Interface\OperationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class Operation implements OperationInterface
{
    private string $format;

    private Store $store;

    /**
     * @param EntityManagerInterface $manager
     * @param Filesystem $filesystem
     * @param ParameterBagInterface $params
     */
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly Filesystem             $filesystem,
        private readonly ParameterBagInterface  $params,
    )
    {

    }

    /**
     * @param string $class
     * @param string $format
     * @param Store $store
     * @return bool
     */
    public function export(string $class, string $format, Store $store): bool
    {
        $this->format = $format;
        $this->store = $store;
        return $this->product($class);
    }

    /**
     * @param string $class
     * @param string $format
     * @param Store $store
     * @return bool
     */
    public function import(string $class, string $format, Store $store): bool
    {
        $this->format = $format;
        return false;
    }

    /**
     * @param string $class
     * @return bool
     */
    private function product(string $class): bool
    {
        $collection = $this->instance($class);
        $revision = time();
        $file = sprintf('%s/%d.%s', $this->storage(), $revision, $this->format);

        switch ($this->format) {
            case EnumOperation::Xml->value:
                $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><products/>');

                foreach ($collection as $item) {
                    $createdAt = $item->getCreatedAt();
                    $child = $xml->addChild('product');
                    $child->addChild('id', (string)$item->getId());
                    $child->addChild('name', $item->getName());
                    $child->addChild('description', strip_tags($item->getDescription()));
                    $child->addChild('cost', (string)$item->getCost());
                    $child->addChild('fee', (string)$item->getFee());
                    $child->addChild('price', number_format($item->getCost() + $item->getFee(), 2, '.', ' '));
                    $child->addChild('quantity', (string)$item->getQuantity());
                    $package = $child->addChild('package', null);
                    $package->addChild('discount', (string)$item->getPckgDiscount());
                    $package->addChild('quantity', (string)$item->getPckgQuantity());
                    $supplier = $child->addChild('supplier', null);
                    $supplier->addChild('name', $item->getStoreProductSupplier()?->getSupplier()?->getName());
                    $supplier->addChild('country', $item->getStoreProductSupplier()?->getSupplier()?->getCountry());
                    $manufacturer = $child->addChild('manufacturer', null);
                    $manufacturer->addChild('name', $item->getStoreProductManufacturer()?->getManufacturer()?->getName());
                    $brand = $child->addChild('brand', null);
                    $brand->addChild('name', $item->getStoreProductBrand()?->getBrand()?->getName());
                    $discount = $child->addChild('discount', null);
                    $discount->addChild('value', (string)$item->getStoreProductDiscount()->getValue());
                    $discount->addChild('unit', $item->getStoreProductDiscount()->getUnit());
                    $child->addChild('createdAt', $createdAt->format('Y-m-d H:i:s'));
                }

                $this->filesystem->dumpFile($file, $xml->asXML());
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown operation format "%s"', $this->format));
        }

        $this->save($revision);

        return $this->filesystem->exists($file);
    }

    /**
     * @param int $revision
     * @return void
     */
    private function save(int $revision): void
    {
        $operation = new StoreOperation();
        $operation->setFormat(EnumOperation::Xml)
            ->setRevision((string)$revision)
            ->setStore($this->store);
        $this->manager->persist($operation);
        $this->manager->flush();
    }

    /**
     * @param string $class
     * @return array|object
     */
    private function instance(string $class): array|object
    {
        return $this->manager->getRepository($class)->findBy(['store' => $this->store]);
    }

    /**
     * @param string|null $format
     * @return string
     */
    public function storage(string $format = null): string
    {
        $storage = $this->params->get('private_storage');
        $format = $format ?? $this->format;

        if (!$this->filesystem->exists($format)) {
            $this->filesystem->mkdir($format);
        }

        return sprintf('%s/%s', $storage, $format);
    }

    /**
     * @param Store $store
     * @param int $offset
     * @param int $limit
     * @return object|array
     */
    public function fetch(Store $store, int $offset = 0, int $limit = 20): object|array
    {
        return $this->manager->getRepository(StoreOperation::class)
            ->findBy(['store' => $store], ['created_at' => 'DESC'], $limit, $offset);
    }
}