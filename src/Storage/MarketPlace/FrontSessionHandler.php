<?php declare(strict_types=1);

namespace App\Storage\MarketPlace;

use App\Service\Redis\ConnectionInterface;
use Psr\Log\LoggerInterface;

class FrontSessionHandler implements FrontSessionInterface
{
    const int TTL = 604800;

    const string NAME = 'rgfly';

    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly LoggerInterface     $logger,
    )
    {
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        try {
            $this->connection->redis()->setex($key, self::TTL, $value);
        } catch (\RedisException $e) {
            $this->logger->critical('{ exception }', [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string
    {
        $value = false;
        try {
            $value = $this->connection->redis()->get($key);
        } catch (\RedisException $e) {
            $this->logger->critical('{ exception }', [
                'exception' => $e->getMessage(),
            ]);
        }

        return is_bool($value) ? (string)$value : $value;
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        try {
            $this->connection->redis()->del($key);
        } catch (\RedisException $e) {
            $this->logger->critical('{ exception }', [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $exists = false;
        try {
            $exists = $this->connection->redis()->exists($key);
        } catch (\RedisException $e) {
            $this->logger->critical('{ exception }', [
                'exception' => $e->getMessage(),
            ]);
        }
        return (bool)$exists;
    }
}