<?php declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\MessageNotification;
use App\Service\Redis\ConnectionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MessageNotificationHandler
{
    const int TTL = 3600 * 24;

    /**
     * @param ConnectionInterface $connection
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly LoggerInterface     $logger,
    )
    {
    }

    /**
     * @param MessageNotification $message
     * @return void
     */
    public function __invoke(MessageNotification $message): void
    {
        $data = json_decode($message->getAnswer(), true);

        try {
            $this->connection->redis()->setex("{$data['recipient']}:{$data['id']}", self::TTL, $message->getAnswer());
        } catch (\RedisException $e) {
            $this->logger->critical('{ exception }', [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}