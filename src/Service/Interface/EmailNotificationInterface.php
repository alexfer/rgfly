<?php

namespace App\Service\Interface;

interface EmailNotificationInterface
{
    /**
     * @param array $args
     * @return void
     */
    public function send(array $args, $template = null): void;
}