<?php declare(strict_types=1);

namespace Inno\Service\Validator\Interface;

interface EmailNotificationInterface
{
    /**
     * @param array $args
     * @param $template
     * @return void
     */
    public function send(array $args, $template = null): void;
}