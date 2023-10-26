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

    use DashboardNavbar;

    /**
     * 
     * @param ContactRepository $reposiroty
     * @return Response
     */
    #[Route('', name: 'app_dashboard_contact')]
    public function index(ContactRepository $reposiroty): Response
    {
        return $this->render('dashboard/content/contact/index.html.twig', $this->build() + [
                    'entries' => $reposiroty->findBy([], ['id' => 'desc']),
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

    /**
     * 
     * @param Request $request
     * @param Contact $entry
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/review/{id}', name: 'app_dashboard_review_contact', methods: ['GET', 'POST'])]
    public function review(
            Request $request,
            Contact $entry,
            EntityManagerInterface $em,
    ): Response
    {


        return $this->render('dashboard/content/contact/review.html.twig', $this->build() + [
                    'entry' => $entry,
        ]);
    }
}
