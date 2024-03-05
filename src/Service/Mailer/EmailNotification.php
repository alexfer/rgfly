<?php

namespace App\Service\Mailer;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

readonly class EmailNotification implements EmailNotificationInterface
{

    /**
     * @param MailerInterface $mailer
     * @param ParameterBagInterface $params
     */
    public function __construct(
        private MailerInterface       $mailer,
        private ParameterBagInterface $params,
    )
    {

    }

    /**
     * @param array $args
     * @param $template
     * @return void
     */
    public function send(array $args, $template = null): void
    {
        $email = (new Email())
            ->from(new Address($args['email'], $args['name']))
            ->to(new Address($this->params->get('app.notifications.email_sender'), $this->params->get('app.notifications.email_sender_name')))
            ->subject($args['subject'] ?: $this->params->get('app.notifications.subject'))
            ->html($template ?: $args['message']);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw $e->getDebug();
        }
    }
}