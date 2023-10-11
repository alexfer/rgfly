<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\Type\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ContactController extends AbstractController
{

    /**
     * 
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @return Response
     */
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(
            Request $request,
            EntityManagerInterface $em,
            ValidatorInterface $validator,
            MailerInterface $mailer,
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
            $em->persist($contact);
            $em->flush();

//            $email = (new Email())
//                    ->from(new Address($form->get('email')->getData(), $form->get('name')->getData()))
//                    ->to(new Address('alexandershtyher@gmail.com', 'Alexander Sh'))
//                    //->cc('cc@example.com')
//                    //->bcc('bcc@example.com')
//                    ->replyTo(new Address('julyshtyher@gmail.com', 'July Sh'))
//                    //->priority(Email::PRIORITY_HIGH)
//                    ->subject($form->get('subject')->getData())
//                    ->text('Sending emails is fun again!')
//                    ->html($form->get('message')->getData());
//
//            try {
//                $mailer->send($email);
//            } catch (TransportExceptionInterface $e) {
//                throw new \Exception($e->getMessage());
//            }

            return $this->redirectToRoute('contact_success');
        }

        return $this->render('contact/index.html.twig', [
                    'errors' => $errors,
                    'form' => $form,
        ]);
    }

    /**
     * 
     * @return Response
     */
    #[Route('/contact/success', name: 'contact_success')]
    public function success(): Response
    {
        return $this->render('contact/success.html.twig', []);
    }
}
