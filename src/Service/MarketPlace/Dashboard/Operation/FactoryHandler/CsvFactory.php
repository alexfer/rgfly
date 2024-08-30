<?php declare(strict_types=1);

namespace App\Service\MarketPlace\Dashboard\Operation\FactoryHandler;

use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Writer;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CsvFactory
{
    /**
     * @var CacheManager
     */
    public CacheManager $cacheManager;

    /**
     * @var array|string[]
     */
    private array $header = [
        'Id',
        'Name',
        'Short Name',
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
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @throws InvalidArgument
     */
    public function __construct(string $delimiter = ',', string $enclosure = '"', string $escape = '\\')
    {
        $this->writer = Writer::createFromString();
        $this->writer->setDelimiter($delimiter);
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
        $data = [];

        foreach ($collection as $item) {

            $createdAt = $item->getCreatedAt();

            $categories = $item->getStoreCategoryProducts();
            $category = $parent = null;

            foreach ($categories as $_category) {
                $category = $_category->getCategory()->getName();
                $parent = $_category->getCategory()->getParent()->getName();
                break;
            }

            $data[] = [
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
            ];
        }

        $this->writer->insertAll($data);

        return $this->writer->toString();
    }
}