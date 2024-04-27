<?php

namespace App\Service\Contact\Interface;

use App\Entity\Contact;
use Symfony\Component\Form\FormInterface;

interface HandleInterface
{
    /**
     * @param FormInterface $form
     * @param Contact $contact
     * @return bool
     */
    public function serve(FormInterface $form, Contact $contact): bool;

    /**
     * @return void
     */
    public function notify(): void;

    /**
     * @param string $email
     * @param string $name
     * @param string $subject
     * @param string $body
     * @return void
     */
    public function answer(string $email, string $name, string $subject, string $body): void;
}