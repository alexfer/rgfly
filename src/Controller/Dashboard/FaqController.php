<?php

namespace App\Controller\Dashboard;

use App\Entity\Faq;
use App\Form\Type\FaqType;
use App\Repository\FaqRepository;
use App\Service\Navbar;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/faq')]
class FaqController extends AbstractController
{

    use Navbar;

    /**
     *
     * @param FaqRepository $reposiroty
     * @param UserInterface $user
     * @return Response
     */
    #[Route('', name: 'app_dashboard_faq')]
    public function index(
        FaqRepository $reposiroty,
        UserInterface $user,
    ): Response
    {
        return $this->render('dashboard/content/faq/index.html.twig', $this->build($user) + [
                'entries' => $reposiroty->findBy([], ['id' => 'desc']),
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
        if ($this->isCsrfTokenValid('delete', $request->get('_token'))) {
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
     * @param UserInterface $user
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/create', name: 'app_dashboard_create_faq', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        EntityManagerInterface $em,
        UserInterface          $user,
    ): Response
    {
        $entry = new Faq();

        $form = $this->createForm(FaqType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();

            return $this->redirectToRoute('app_dashboard_edit_faq', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/faq/_form.html.twig', $this->build($user) + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param Faq $entry
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{id}', name: 'app_dashboard_edit_faq', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        Faq                    $entry,
        EntityManagerInterface $em,
        UserInterface          $user,
    ): Response
    {
        $form = $this->createForm(FaqType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();

            return $this->redirectToRoute('app_dashboard_edit_faq', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/faq/_form.html.twig', $this->build($user) + [
                'form' => $form,
            ]);
    }
}
