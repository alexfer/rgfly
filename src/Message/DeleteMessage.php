<?php declare(strict_types=1);

namespace Inno\Message;

readonly class DeleteMessage
{
    /**
     * @param string $message
     */
    public function __construct(
        private string $message,
    )
    {
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}