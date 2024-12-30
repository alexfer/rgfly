<?php declare(strict_types=1);

namespace Inno\Service\Redis;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Connection implements ConnectionInterface
{
    /**
     * @var string|null
     */
    private ?string $dsn = null;

    /**
     * @param LoggerInterface $logger
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        ParameterBagInterface            $bag
    )
    {
        $this->dsn = $this->dsn ?: $bag->get('app.redis.dsn');
    }

    /**
     * @return \Redis
     */
    public function redis(): \Redis
    {
        $redis = new \Redis();

        try {
            $redis->connect($this->dsn);
        } catch (\RedisException $e) {
            $this->logger->critical('{ exception }', [
                'exception' => $e->getMessage(),
            ]);
        }
        return $redis;
    }
}