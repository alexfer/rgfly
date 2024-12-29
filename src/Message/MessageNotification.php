<?php declare(strict_types=1);

namespace Essence\Message;

readonly class MessageNotification
{
    /**
     * @param string $answer
     */
    public function __construct(
        private string $answer,
    )
    {
    }

    /**
     * @return string
     */
    public function getAnswer(): string
    {
        return $this->answer;
    }
}