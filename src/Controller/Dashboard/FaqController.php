<?php

namespace App\Controller\Dashboard;

use App\Entity\Faq;
use App\Form\Type\FaqType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/faq')]
class FaqController extends AbstractController
{
    /**
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    #[Route('', name: 'app_dashboard_faq')]
    public function index(
        EntityManagerInterface $em,
        UserInterface          $user,
    ): Response
    {
        return $this->render('dashboard/content/faq/index.html.twig', [
            'entries' => $em->getRepository(Faq::class)->findBy([], ['id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param Faq $entry
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'app_dashboard_delete_faq', methods: ['POST'])]
    public function delete(
        Request                $request,
        Faq                    $entry,
        EntityManagerInterface $em,
    ): Response
    {
        $token = $request->get('_token');

        if (!$token) {
            $content = $request->getPayload()->all();
            $token = $content['_token'];
        }

        if ($this->isCsrfTokenValid('delete', $token)) {
            $date = new DateTime('@' . strtotime('now'));
            $entry->setDeletedAt($date)->setVisible(false);
            $em->persist($entry);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_faq');
    }

    /**
     *
     * @param Faq $entry
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/restore/{id}', name: 'app_dashboard_restore_faq')]
    public function restore(
        Faq                    $entry,
        EntityManagerInterface $em,
    ): Response
    {
        $entry->setDeletedAt(null);
        $em->persist($entry);
        $em->flush();

        return $this->redirectToRoute('app_dashboard_faq');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/create', name: 'app_dashboard_create_faq', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $entry = new Faq();

        $form = $this->createForm(FaqType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_edit_faq', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/faq/_form.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @param Request $request
     * @param Faq $entry
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/edit/{id}', name: 'app_dashboard_edit_faq', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        Faq                    $entry,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $form = $this->createForm(FaqType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));
            return $this->redirectToRoute('app_dashboard_edit_faq', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/faq/_form.html.twig', [
            'form' => $form,
        ]);
    }
}
