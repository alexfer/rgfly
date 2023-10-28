<?php

namespace App\Controller\Security;

use App\Form\Type\User\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     *
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/web/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(
        Request             $request,
        AuthenticationUtils $authenticationUtils,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_index');
        }

        $default = [
            'email' => $authenticationUtils->getLastUsername(),
        ];

        $form = $this->createForm(LoginType::class, $default);
        $form->handleRequest($request);

        $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
        $request->getSession()->clear();

        return $this->render('security/index.html.twig', [
            'form' => $form->createView(),
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $error,
        ]);
    }

    /**
     *
     * @return Response
     * @throws \Exception
     */
    #[Route('/web/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     *
     * @return Response
     * @throws \Exception
     */
    #[Route('/login/error', name: 'app_login_error')]
    public function error(Request $request): Response
    {
        return $this->render('security/error.html.twig', [
            'error' => $request->getSession()->get(Security::AUTHENTICATION_ERROR),
        ]);
    }
}
