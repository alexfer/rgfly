<?php

namespace App\Message;

class MessageNotification
{
    /**
     * @param string $answer
     */
    public function __construct(
        private readonly string $answer,
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