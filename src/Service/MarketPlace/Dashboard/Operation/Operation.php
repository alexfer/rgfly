<?php

declare(strict_types=1);

namespace App\Service\MarketPlace\Dashboard\Operation;

use App\Entity\MarketPlace\Enum\EnumOperation;
use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreOperation;
use App\Entity\MarketPlace\StoreProduct;
use App\Service\MarketPlace\Dashboard\Operation\Interface\OperationInterface;

class Operation extends Handler implements OperationInterface
{

    /**
     * @param string $class
     * @param array $options
     * @param Store $store
     * @return bool
     */
    public function export(string $class, array $options, Store $store): bool
    {
        $this->options = $options;
        $this->store = $store;
        return $this->product($class);
    }

    /**
     * @param string $class
     * @param array $options
     * @param Store $store
     * @return bool
     */
    public function import(string $class, array $options, Store $store): bool
    {
        $this->options = $options;
        return false;
    }

    /**
     * @param string $class
     * @return bool
     */
    private function product(string $class): bool
    {
        $revision = time();
        $format = $this->options['format'];
        $file = sprintf('%s/%d.%s', $this->storage(), $revision, $format);

        switch ($format) {
            case EnumOperation::Xml->value:
                $this->xml($file, $class, $revision);
                break;
            case EnumOperation::Csv->value:
                $this->csv($file, $class, $revision);
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown operation format "%s"', $format));
        }


        return $this->filesystem->exists($file);
    }

    /**
     * @param string|null $format
     * @return string
     */
    public function storage(string $format = null): string
    {
        $storage = $this->params->get('private_storage');
        $format = $format ?? $this->options['format'];

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

    /**
     * @return array
     */
    public function metadata(): array
    {
        $class = $this->manager->getClassMetadata(StoreProduct::class);

        $exclude = ['id', 'updated_at', 'deleted_at'];
        $include = ['price', 'supplier', 'brand', 'manufacturer', 'category', 'image', 'discount'];
        $fields = [];

        if (!empty($class->discriminatorColumn)) {
            $fields[] = $class->discriminatorColumn['name'];
        }

        $fields = array_merge($class->getColumnNames(), $fields);

        foreach ($fields as $index => $field) {
            if ($class->isInheritedField($field)) {
                unset($fields[$index]);
            }
        }
        $fields = array_diff($fields, $exclude);
        return array_merge($fields, $include);
    }
}