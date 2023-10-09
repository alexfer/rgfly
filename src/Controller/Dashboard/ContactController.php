<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ContactRepository;
use App\Entity\Contact;

#[Route('/dashboard/contact')]
class ContactController extends AbstractController
{

    #[Route('/', name: 'app_dashboard_contact')]
    public function index(ContactRepository $reposiroty): Response
    {
        return $this->render('dashboard/content/contact/index.html.twig', [
                    'entries' => $reposiroty->findBy([], ['id' => 'desc']),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_contact_delete', methods: ['POST'])]
    public function delete(
            Request $request,
            Contact $entry,
            EntityManagerInterface $em,
    ): Response
    {
        if ($this->isCsrfTokenValid('delete', $request->get('_token'))) {
            $em->remove($entry);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_contact');
    }
}
