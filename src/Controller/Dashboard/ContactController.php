<?php

namespace App\Controller\Dashboard;

use App\Entity\Answer;
use App\Entity\Contact;
use App\Service\Contact\Interface\HandleInterface;
use App\Service\Dashboard;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/contact')]
class ContactController extends AbstractController
{

    use Dashboard;

    /**
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    #[Route('', name: 'app_dashboard_contact')]
    public function index(
        EntityManagerInterface $em,
        UserInterface          $user,
    ): Response
    {
        return $this->render('dashboard/content/contact/index.html.twig', $this->navbar() + [
                'entries' => $em->getRepository(Contact::class)->findBy([], ['id' => 'desc']),
            ]);
    }

    /**
     *
     * @param Request $request
     * @param Contact $entry
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/delete/{id}', name: 'app_dashboard_delete_contact', methods: ['POST'])]
    public function delete(
        Request                $request,
        Contact                $entry,
        EntityManagerInterface $em,
    ): Response
    {

        $token = $request->get('_token');

        if ($request->headers->get('Content-Type', 'application/json')) {
            $content = $request->getContent();
            $content = json_decode($content, true);
            $token = $content['_token'];
        }

        if ($this->isCsrfTokenValid('delete', $token)) {
            $entry->setStatus($entry::STATUS['trashed']);
            $em->persist($entry);
            $em->flush();
        }

        if ($request->headers->get('Content-Type', 'application/json')) {
            return $this->json(['redirect' => $this->generateUrl('app_dashboard_contact')]);
        }

        return $this->redirectToRoute('app_dashboard_contact');
    }

    /**
     * @param Request $request
     * @param Contact $contact
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    #[Route('/review/{id}', name: 'app_dashboard_review_contact', methods: ['GET', 'POST'])]
    public function review(
        Request                $request,
        Contact                $contact,
        HandleInterface        $handle,
        EntityManagerInterface $em,
        UserInterface          $user,
    ): Response
    {

        if ($request->getMethod() == 'POST') {
            $message = $request->request->get('answer');
            $answer = new Answer();
            $answer->setContact($contact)
                ->setUser($user)
                ->setMessage($message);
            $em->persist($answer);
            $em->flush();

            $handle->answer(
                $contact->getEmail(),
                $contact->getName(),
                sprintf("RE: %s", $contact->getSubject()),
                $message
            );

            return $this->redirectToRoute('app_dashboard_review_contact', ['id' => $contact->getId()]);
        }

        return $this->render('dashboard/content/contact/review.html.twig', $this->navbar() + [
                'contact' => $contact,
            ]);
    }
}
