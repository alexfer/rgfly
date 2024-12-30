<?php declare(strict_types=1);

namespace Inno\Service\MarketPlace\Mail;

interface SendMailInterface
{
    /**
     * @param array $data
     * @return void
     */
    public function send(array $data): void;
}