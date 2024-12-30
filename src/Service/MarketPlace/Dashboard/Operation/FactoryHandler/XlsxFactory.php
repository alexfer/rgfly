<?php declare(strict_types=1);

namespace Inno\Service\MarketPlace\Dashboard\Operation\FactoryHandler;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class XlsxFactory
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
        'Image Url',
        'Created At'
    ];

    /**
     * @var ParameterBagInterface
     */
    public ParameterBagInterface $params;

    /**
     * @var Spreadsheet
     */
    private Spreadsheet $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
    }

    /**
     * @param int $revision
     * @param array|object $collection
     * @param array $option
     * @return void
     */
    public function build(int $revision, array|object $collection, array $option): void
    {
        $this->spreadsheet->removeSheetByIndex(0);

        $worksheet = new Worksheet($this->spreadsheet, 'Products');
        $this->spreadsheet->addSheet($worksheet, 0);

        $data[] = $this->header;

        foreach ($collection as $item) {

            $createdAt = $item->getCreatedAt();

            $categories = $item->getStoreCategoryProducts();
            $category = $parent = $picture = null;

            foreach ($categories as $_category) {
                $category = $_category->getCategory()->getName();
                $parent = $_category->getCategory()->getParent()->getName();
                break;
            }

            $images = $item->getStoreProductAttaches();

            foreach ($images as $attach) {
                $url = sprintf('%s/%d', $this->params->get('product_storage_dir'), $item->getId());
                $picture = $this->cacheManager->getBrowserPath(parse_url($url . '/' . $attach->getAttach()->getName(), PHP_URL_PATH), 'product_view', [], null);
            }

            $data[] = [
                $item->getId(),
                $item->getName(),
                $item->getShortName(),
                $item->getDescription(),
                $item->getCost(),
                $item->getFee(),
                number_format(($item->getCost() + $item->getFee()), 2, '.', ' '),
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
                $picture,
                $createdAt->format('Y-m-d H:i')
            ];
        }

        $worksheet->fromArray($data);
        $worksheets = [$worksheet];

        foreach ($worksheets as $sheet) {
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false);
        $writer->save($this->params->get('private_storage') . sprintf('/xlsx/%d.xlsx', $revision));
    }
}