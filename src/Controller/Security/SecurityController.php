<?php

namespace App\Controller\Security;

use App\Form\Type\User\LoginType;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class SecurityController extends AbstractController
{
    /**
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/web/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(
        Request             $request,
        AuthenticationUtils $authenticationUtils,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_dashboard');
        }

        $default = [
            'email' => $authenticationUtils->getLastUsername(),
        ];

        $form = $this->createForm(LoginType::class, $default);
        $form->handleRequest($request);

        $error = $request->getSession()->get(SecurityRequestAttributes::AUTHENTICATION_ERROR);
        $quantity = $request->getSession()->get('quantity');
        //$request->getSession()->clear();

        return $this->render('security/index.html.twig', [
            'form' => $form->createView(),
            'last_username' => $default['email'],
            'error' => $error,
            'quantity' => $quantity,
        ]);
    }

    /**
     *
     * @return Response
     * @throws Exception
     */
    #[Route('/web/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // controller can be blank: it will never be called!
        throw new Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/login/error', name: 'app_login_error')]
    public function error(Request $request): Response
    {
        return $this->render('security/error.html.twig', [
            'error' => $request->getSession()->get(SecurityRequestAttributes::AUTHENTICATION_ERROR),
        ]);
    }
}
