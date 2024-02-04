<?php

namespace App\Storage\MarketPlace;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorage implements SessionStorageInterface
{
    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function has(string $name): ?string
    {
        return $this->session->has($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set(string $name, mixed $value): void
    {
        $this->session->set($name, $value);
    }

    /**
     * @param string $name
     * @param $default
     * @return string
     */
    public function get(string $name, $default = null): string
    {
        return $this->session->get($name, $default);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->session->all();
    }

    /**
     * @param string $name
     * @return void
     */
    public function remove(string $name): void
    {
        $this->session->remove($name);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->session->clear();
    }
}