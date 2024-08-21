<?php

namespace App\Service\MarketPlace\Dashboard\Operation;

use App\Entity\MarketPlace\Enum\EnumOperation;
use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreOperation;
use App\Service\MarketPlace\Dashboard\Operation\Handler\CsvFactory;
use App\Service\MarketPlace\Dashboard\Operation\Handler\XmlFactory;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class Handler
{
    protected array $options;

    protected Store $store;

    /**
     * @param EntityManagerInterface $manager
     * @param Filesystem $filesystem
     * @param ParameterBagInterface $params
     * @param CacheManager $cacheManager
     */
    public function __construct(
        protected readonly EntityManagerInterface $manager,
        protected readonly Filesystem             $filesystem,
        protected readonly ParameterBagInterface  $params,
        protected readonly CacheManager           $cacheManager,
    )
    {

    }

    /**
     * @param string $file
     * @param string $class
     * @param int $revision
     * @return void
     */
    protected function xml(string $file, string $class, int $revision): void
    {
        $collection = $this->instance($class);
        $option = $this->options['option'];

        $xml = new XmlFactory('products');
        $xml->cacheManager = $this->cacheManager;
        $xml->params = $this->params;

        $xml = $xml->build($collection, $option);

        $this->save($revision, $this->options['format']);
        $this->filesystem->dumpFile($file, $xml);
    }

    /**
     * @param string $file
     * @param string $class
     * @param int $revision
     * @return void
     * @throws CannotInsertRecord
     * @throws Exception
     */
    protected function csv(string $file, string $class, int $revision): void
    {
        $collection = $this->instance($class);
        $option = $this->options['option'];

        $csv = new CsvFactory();
        $csv->cacheManager = $this->cacheManager;
        $csv->params = $this->params;

        $csv = $csv->build($collection, $option);

        $this->save($revision, $this->options['format']);
        $this->filesystem->dumpFile($file, $csv);
    }


    /**
     * @param int $revision
     * @param $format
     * @return void
     */
    private function save(int $revision, $format): void
    {
        $operation = new StoreOperation();
        $operation->setFormat(EnumOperation::from($format))
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
}