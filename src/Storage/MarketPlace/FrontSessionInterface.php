<?php declare(strict_types=1);

namespace Essence\Storage\MarketPlace;

interface FrontSessionInterface
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string;

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}