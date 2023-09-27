<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Form;
use App\Form\Type\User\LoginType;

class SecurityController extends AbstractController
{

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(
            Request $request,
            AuthenticationUtils $authenticationUtils,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_index');
        }

        $default = [
            'username' => $authenticationUtils->getLastUsername(),
        ];

        $form = $this->createForm(LoginType::class, $default);
        $form->handleRequest($request);

        $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
        $request->getSession()->clear();

        return $this->render('security/index.html.twig', [
                    'form' => $form->createView(),
                    'last_username' => $authenticationUtils->getLastUsername(),
                    'error' => $error,
                    'errors' => $this->getErrorMessages($form),
        ]);
    }

    /**
     * 
     * @param Form $form
     * @return array
     */
    public function getErrorMessages(Form $form): array
    {

        $errors = [];

        foreach ($form->all() as $child) {
            foreach ($child->getErrors() as $error) {
                $name = $child->getName();
                $errors[$name]['message'] = $error->getMessage();
            }
        }

        return $errors;
    }

    /**
     * 
     * @return Response
     * @throws \Exception
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
