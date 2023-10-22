<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ContactRepository;
use App\Service\DashboardNavbar;
use App\Entity\Contact;

#[Route('/dashboard/contact')]
class ContactController extends AbstractController
{

    #[Route('/', name: 'app_dashboard_contact')]
    public function index(ContactRepository $reposiroty): Response
    {
        return $this->render('dashboard/content/contact/index.html.twig', DashboardNavbar::build() + [
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
            $entry->setStatus($entry::STATUS['trashed']);
            $em->persist($entry);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_contact');
    }

    #[Route('/review/{id}', name: 'app_dashboard_contact_review', methods: ['GET', 'POST'])]
    public function review(
            Request $request,
            Contact $entry,
            EntityManagerInterface $em,
    ): Response
    {


        return $this->render('dashboard/content/contact/review.html.twig', DashboardNavbar::build() + [
                    'entry' => $entry,
        ]);
    }
}
