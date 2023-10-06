<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\Type\User\FacebookType;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{

    #[Route('/connect/facebook', name: 'facebook_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to Facebook!
        return $clientRegistry
                        ->getClient('facebook') // key used in config/packages/knpu_oauth2_client.yaml
                        ->redirect([
                            'public_profile', 'email' // the scopes you want to access
        ]);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     */
    #[Route('/connect/facebook/check', name: 'facebook_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)

        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $clientRegistry->getClient('facebook');

        try {
            // the exact class depends on which provider you're using
            /** @var \League\OAuth2\Client\Provider\FacebookUser $user */
            $user = $client->fetchUser();

            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
            var_dump($user);
            die;
            // ...
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            var_dump($e->getMessage());
            die;
        }
    }

    #[Route('/connect/facebook/registration', name: 'facebook_registration')]
    public function finishRegistration(Request $request)
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->get('app.facebook_authenticator')
                ->getUserInfoFromSession($request);
        if (!$facebookUser) {
            throw $this->createNotFoundException('How did you get here without user information!?');
        }
        $user = new User();
        $user->setFacebookId($facebookUser->getId());
        $user->setEmail($facebookUser->getEmail());

        $form = $this->createForm(FacebookType::class, $user);

        $form->handleRequest($request);
        if ($form->isValid()) {
            // encode the password manually
            $plainPassword = $form['plainPassword']->getData();
            $encodedPassword = $this->get('security.password_encoder')
                    ->encodePassword($user, $plainPassword);
            $user->setPassword($encodedPassword);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // remove the session information
            $request->getSession()->remove('facebook_user');

            // log the user in manually
            $guardHandler = $this->container->get('security.authentication.guard_handler');
            return $guardHandler->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $this->container->get('app.facebook_authenticator'),
                            'main' // the firewall key
            );
        }

        return $this->render('security/social/fasebook/registration.html.twig', [
                    'form' => $form->createView(),
        ]);
    }
}
