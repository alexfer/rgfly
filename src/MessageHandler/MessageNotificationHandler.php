<?php

namespace App\MessageHandler;

use App\Message\MessageNotification;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MessageNotificationHandler
{
    const int TTL = 3600;

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
     * @param MessageNotification $message
     * @return void
     */
    public function __invoke(MessageNotification $message)
    {
        $redis = new \Redis();
        $data = json_decode($message->getAnswer(), true);

        try {
            $redis->connect($this->dsn);
        } catch (\RedisException $e) {
            $this->logger->critical('{ exception }', [
                'exception' => $e->getMessage(),
            ]);
        }

        try {
            $redis->setex("mess_{$data['id']}:{$data['parent']}:{$data['store']}", self::TTL, $message->getAnswer());
        } catch (\RedisException $e) {
            $this->logger->critical('{ exception }', [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}