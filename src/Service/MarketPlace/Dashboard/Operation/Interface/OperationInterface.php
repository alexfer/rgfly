<?php

declare(strict_types=1);

namespace App\Service\MarketPlace\Dashboard\Operation\Interface;

use App\Entity\MarketPlace\Store;

interface OperationInterface
{
    /**
     * @param string $class
     * @param string $format
     * @param Store $store
     * @return bool
     */
    public function export(string $class, string $format, Store $store): bool;

    /**
     * @param string $class
     * @param string $format
     * @param Store $store
     * @return bool
     */
    public function import(string $class, string $format, Store $store): bool;

    /**
     * @param Store $store
     * @param int $offset
     * @param int $limit
     * @return object|array
     */
    public function fetch(Store $store, int $offset, int $limit): object|array;

    /**
     * @param string|null $format
     * @return string
     */
    public function storage(string $format = null): string;
}