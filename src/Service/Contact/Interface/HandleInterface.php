<?php

namespace App\Service\Contact\Interface;

use App\Entity\Contact;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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


    public function answer(Contact $contact, UserInterface $user, string $message): void;
}