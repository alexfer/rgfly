<?php

namespace App\MessageHandler;

use App\Message\DeleteMessage;
use App\Service\Redis\ConnectionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteMessageNotificationHandler
{
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
     * @param DeleteMessage $message
     * @return void
     */
    public function __invoke(DeleteMessage $message): void
    {

        try {
            $this->connection->redis()->del($message->getMessage());
        } catch (\RedisException $e) {
            $this->logger->critical('{ exception }', [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}