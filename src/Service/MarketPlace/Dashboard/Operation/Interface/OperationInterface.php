<?php

declare(strict_types=1);

namespace App\Service\MarketPlace\Dashboard\Operation\Interface;

interface OperationInterface
{
    /**
     * @param string $class
     * @param string $format
     * @param int $store
     * @return bool
     */
    public function export(string $class, string $format, int $store): bool;

    /**
     * @param string $class
     * @param string $format
     * @param int $store
     * @return bool
     */
    public function import(string $class, string $format, int $store): bool;
}