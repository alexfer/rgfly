<?php

namespace App\Controller\Dashboard;

use DateTime;
use App\Entity\{Entry, EntryDetails};
use App\Form\Type\Dashboard\EntryDetailsType;
use App\Repository\EntryRepository;
use App\Service\Dashboard;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response,};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/dashboard/blog')]
class BlogController extends AbstractController
{

    use Dashboard;

    const CHILDRENS = [
        'blog' => [
            'menu.dashboard.overview.blog' => 'app_dashboard_blog',
            'menu.dashboard.create.blog' => 'app_dashboard_create_blog',
        ],
    ];

    #[Route('', name: self::CHILDRENS['blog']['menu.dashboard.overview.blog'])]
    public function index(
        EntryRepository $repository,
        UserInterface   $user,
    ): Response
    {
        $entries = $repository->findBy($this->criteria($user, ['type' => 'blog']), ['id' => 'desc']);

        return $this->render('dashboard/content/blog/index.html.twig', $this->build($user) + [
                'entries' => $entries,
            ]);
    }

    #[Route('/create', name: self::CHILDRENS['blog']['menu.dashboard.create.blog'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $entry = new Entry();
        $details = new EntryDetails();

        $form = $this->createForm(EntryDetailsType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entry->setType('blog')
                ->setUser($user);

            $em->persist($entry);
            $em->flush();

            $details->setTitle($form->get('title')->getData())
                ->setContent($form->get('content')->getData())
                ->setEntry($entry);

            $em->persist($details);
            $em->flush();

            return $this->redirectToRoute('app_dashboard_edit_blog', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/blog/_form.html.twig', $this->build($user) + [
                'form' => $form,
            ]);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_edit_blog', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'entry')]
    public function edit(
        Request                $request,
        Entry                  $entry,
        EntityManagerInterface $em,
        UserInterface          $user,
    ): Response
    {
        $form = $this->createForm(EntryDetailsType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entry->setStatus($form->get('status')->getData())
                ->setUpdatedAt(new DateTime())
                ->setDeletedAt($form->get('status')->getData() == 'trashed' ? new DateTime() : null);

            $em->persist($entry);
            $em->flush();

            $details = $entry->getEntryDetails();

            $details->setTitle($form->get('title')->getData())
                ->setContent($form->get('content')->getData())
                ->setEntry($entry);

            $em->persist($details);
            $em->flush();

            return $this->redirectToRoute('app_dashboard_edit_blog', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/blog/_form.html.twig', $this->build($user) + [
                'form' => $form,
            ]);
    }
}
