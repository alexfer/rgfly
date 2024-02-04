<?php

namespace App\Storage\MarketPlace;

interface SessionStorageInterface
{
    /**
     * @param string $name
     * @return string|null
     */
    public function has(string $name): ?string;

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set(string $name, mixed $value): void;

    /**
     * @param string $name
     * @param mixed|null $default
     * @return string|null
     */
    public function get(string $name, mixed $default = null): ?string;

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string $name
     * @return void
     */
    public function remove(string $name): void;

    /**
     * @return void
     */
    public function clear(): void;
}