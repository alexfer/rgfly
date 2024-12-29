<?php declare(strict_types=1);

namespace Essence\Service\MarketPlace\Mail;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

readonly class Send implements SendMailInterface
{

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(private MailerInterface $mailer)
    {

    }

    /**
     * @param string $email
     * @param string $name
     * @return Address
     */
    private function address(string $email, string $name): Address
    {
        return new Address($email, $name);
    }

    /**
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function send(array $data): void
    {
        $mail = new Email();

        $mail->from($this->address($data['from']['email'], $data['from']['name']))
            ->to($this->address($data['to']['email'], $data['to']['name']))
            ->subject($data['subject'])
            ->attachFromPath($data['attachment'])
            ->html($data['body']);
        try {
            $this->mailer->send($mail);
            unlink($data['attachment']);
        } catch (TransportExceptionInterface $exception) {
            throw new \Exception('Mailer error: ' . $exception->getMessage());
        }

    }
}