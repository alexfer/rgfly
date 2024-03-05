<?php

namespace App\Service\Mailer;

interface EmailNotificationInterface
{
    /**
     * @param array $args
     * @return void
     */
    public function send(array $args, $template = null): void;
}