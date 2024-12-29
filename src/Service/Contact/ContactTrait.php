<?php

namespace Essence\Service\Contact;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

trait ContactTrait
{
    /**
     * @return Response
     */
    #[Route('/contact/success', name: 'contact_success')]
    public function success(): Response
    {
        return $this->render('contact/success.html.twig', []);
    }
}