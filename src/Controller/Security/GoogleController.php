<?php

namespace App\Controller\Security;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'profile'
            ]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheck(Request $request, ClientRegistry $clientRegistry)
    {
        $client = $clientRegistry->getClient('google');

        try {
            $user = $client->fetchUser();
            dd($user);
        } catch (IdentityProviderException $e) {
            dd($e->getMessage());
        }
    }
}