<?php

namespace App\Controller\Dashboard;

use App\Entity\Answer;
use App\Entity\Contact;
use App\Service\Contact\Interface\HandleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/contact')]
class ContactController extends AbstractController
{

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
        return $this->render('dashboard/content/contact/index.html.twig',  [
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

        if (!$token) {
            $content = $request->getPayload()->all();
            $token = $content['_token'];
        }

        if ($this->isCsrfTokenValid('delete', $token)) {
            $entry->setStatus($entry::STATUS['trashed']);
            $em->persist($entry);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_contact');
    }

    /**
     * @param Request $request
     * @param Contact $contact
     * @param HandleInterface $handle
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param UserInterface $user
     * @return Response
     */
    #[Route('/review/{id}', name: 'app_dashboard_review_contact', methods: ['GET', 'POST'])]
    public function review(
        Request                $request,
        Contact                $contact,
        HandleInterface        $handle,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
        UserInterface          $user,
    ): Response
    {
        if ($request->getMethod() == 'POST') {
            $message = $request->request->get('answer');

            if (!$message && strlen($message) < 3) {
                $error = $translator->trans('message.danger.text');
                $this->addFlash('danger', json_encode(['message' => $error]));
                $this->addFlash('error', $error);
            } else {
                $this->addFlash('success', json_encode(['message' => $translator->trans('message.success.text')]));
                $handle->answer($contact, $user, $message);
            }

            return $this->redirectToRoute('app_dashboard_review_contact', ['id' => $contact->getId()]);
        }

        return $this->render('dashboard/content/contact/review.html.twig',  [
                'contact' => $contact,
                'answers' => $em->getRepository(Answer::class)->findBy(['contact' => $contact->getId()], ['id' => 'desc']),
            ]);
    }
}
