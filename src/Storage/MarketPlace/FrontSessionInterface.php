<?php declare(strict_types=1);

namespace App\Storage\MarketPlace;

interface FrontSessionInterface
{

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void;

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

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function push(string $key, mixed $value): void;
}