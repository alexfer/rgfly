<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request,
    Response,
};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Form;

//use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
            Request $request,
            UserPasswordHasherInterface $userPasswordHasher,
            EntityManagerInterface $em,
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                    $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                    )
            );

            $em->persist($user);
            $em->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_register_success');
        }

        return $this->render('registration/register.html.twig', [
                    'errors' => $this->getErrorMessages($form),
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/register/success', name: 'app_register_success')]
    public function success(): Response
    {
        return $this->render('registration/success.html.twig', []);
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
}
