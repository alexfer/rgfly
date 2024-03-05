<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\Type\ContactType;
use App\Service\Mailer\EmailNotificationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param EmailNotificationInterface $emailNotification
     * @return Response
     */
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(
        Request                    $request,
        EntityManagerInterface     $em,
        EmailNotificationInterface $emailNotification,
        ParameterBagInterface $params,
    ): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contact);
            $em->flush();

            $args = $request->request->all();
            $template = $this->renderView('mail/default.html.twig', [
                'args' => $args['contact'],
                'subject' => $params->get('app.notifications.subject'),
                'index' => $this->generateUrl('app_index')
            ]);
            $emailNotification->send($args['contact'], $template);

            return $this->redirectToRoute('contact_success');
        }

        return $this->render('contact/index.html.twig', [
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
