<?php

namespace App\Security\Authentication;

use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class FacebookAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{

    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('facebook_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
                new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                            /** @var FacebookUser $facebookUser */
                            $facebookUser = $client->fetchUserFromToken($accessToken);

                            $email = $facebookUser->getEmail();

                            // 1) have they logged in with Facebook before? Easy!
                            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['facebookId' => $facebookUser->getId()]);

                            if ($existingUser) {
                                return $existingUser;
                            }

                            // 2) do we have a matching user by email?
                            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                            // 3) Maybe you just want to "register" them by creating
                            // a User object
                            $user->setFacebookId($facebookUser->getId());
                            $this->entityManager->persist($user);
                            $this->entityManager->flush();

                            return $user;
                        })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('app_index');

        return new RedirectResponse($targetUrl);

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
                '/connect/', // might be the site, where users choose their oauth provider
                Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
