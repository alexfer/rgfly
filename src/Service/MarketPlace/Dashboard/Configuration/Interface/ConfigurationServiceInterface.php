<?php declare(strict_types=1);

namespace App\Service\MarketPlace\Dashboard\Configuration\Interface;

interface ConfigurationServiceInterface
{
    /**
     * @param string|null $target
     * @param mixed|null $id
     * @return self
     */
    public function take(?string $target = null, mixed $id = null): self;

    /**
     * @param array|null $criteria
     * @param array|null $order
     * @return array
     */
    public function collection(?array $criteria = [], ?array $order = []): array;

    /**
     * @return bool
     */
    public function remove(): bool;
}