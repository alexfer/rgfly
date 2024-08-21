<?php

namespace App\Service\MarketPlace\Dashboard\Operation\Handler;

use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Writer;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CsvFactory
{
    public CacheManager $cacheManager;

    private array $header = [
        'Id',
        'Name',
        'Shor Name',
        'Description',
        'Cost',
        'Fee',
        'Price',
        'Quantity',
        'Pckg Quantity',
        'Pkg Discount',
        'Discount Value',
        'Discount Unit',
        'Category Name',
        'Category Parent Name',
        'Supplier',
        'Brand',
        'Manufacturer',
        'Created At'
    ];

    /**
     * @var ParameterBagInterface
     */
    public ParameterBagInterface $params;

    /**
     * @var Writer
     */
    private Writer $writer;

    /**
     * @throws InvalidArgument
     */
    public function __construct()
    {
        $this->writer = Writer::createFromString();
        $this->writer->setDelimiter(',');
    }

    /**
     * @param array|object $collection
     * @param array $option
     * @return string
     * @throws CannotInsertRecord
     * @throws Exception
     */
    public function build(array|object $collection, array $option): string
    {
        $this->writer->insertOne($this->header);

        foreach ($collection as $item) {

            $createdAt = $item->getCreatedAt();

            $categories = $item->getStoreCategoryProducts();
            $category = $parent = null;

            foreach ($categories as $_category) {
                $category = $_category->getCategory()->getName();
                $parent = $_category->getCategory()->getParent()->getName();
                break;
            }

            $this->writer->insertOne([
                $item->getId(),
                $item->getName(),
                $item->getShortName(),
                $item->getDescription(),
                $item->getCost(),
                $item->getFee(),
                number_format(($item->getCost() + $item->getFee()), 2,'.', ' '),
                $item->getQuantity(),
                $item->getPckgQuantity(),
                $item->getPckgDiscount(),
                $item->getStoreProductDiscount()->getValue(),
                $item->getStoreProductDiscount()->getUnit(),
                $category,
                $parent,
                $item->getStoreProductSupplier()?->getSupplier()?->getName(),
                $item->getStoreProductBrand()?->getBrand()?->getName(),
                $item->getStoreProductManufacturer()?->getManufacturer()?->getName(),
                $createdAt->format('Y-m-d H:i:s')
            ]);
        }

        return $this->writer->toString();
    }
}