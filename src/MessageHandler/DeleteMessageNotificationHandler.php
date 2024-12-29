<?php declare(strict_types=1);

namespace Essence\MessageHandler;

use Essence\Message\DeleteMessage;
use Essence\Service\Redis\ConnectionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class DeleteMessageNotificationHandler
{
    /**
     * @param ConnectionInterface $connection
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ConnectionInterface $connection,
        private LoggerInterface     $logger,
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