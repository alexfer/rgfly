<?php declare(strict_types=1);

namespace Essence\Service\Contact\Interface;

use Essence\Entity\Contact;
use Essence\Entity\User;
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


    public function answer(Contact $contact, User $user, string $message): void;
}