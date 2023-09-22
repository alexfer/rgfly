<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Contact;
use App\Form\Type\ContactType;

class ContactController extends AbstractController
{

    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(
            Request $request,
            EntityManagerInterface $em,
            ValidatorInterface $validator,
    ): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        $errors = null;
        if ($request->isMethod('POST')) {
            $errors = $validator->validate($contact);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            
        }

        return $this->render('contact/index.html.twig', [
                    'errors' => $errors,
                    'form' => $form,
        ]);
    }
}
