<?php

namespace App\Controller\Dashboard;

use App\Entity\{Entry, EntryCategory, EntryDetails};
use App\Form\Type\Dashboard\EntryDetailsType;
use App\Repository\CategoryRepository;
use App\Repository\EntryCategoryRepository;
use App\Repository\EntryRepository;
use App\Service\Navbar;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response,};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/dashboard/blog')]
class BlogController extends AbstractController
{
    use Navbar;

    const CHILDREN = [
        'blog' => [
            'menu.dashboard.overview.blog' => 'app_dashboard_blog',
            'menu.dashboard.create.blog' => 'app_dashboard_create_blog',
        ],
    ];

    /**
     * @param EntryRepository $repository
     * @param UserInterface $user
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('', name: self::CHILDREN['blog']['menu.dashboard.overview.blog'])]
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

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param CategoryRepository $category
     * @param CategoryRepository $categoryRepository
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/create', name: self::CHILDREN['blog']['menu.dashboard.create.blog'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        CategoryRepository     $category,
        CategoryRepository     $categoryRepository,
        SluggerInterface       $slugger,
    ): Response
    {
        $entry = new Entry();
        $details = new EntryDetails();

        $form = $this->createForm(EntryDetailsType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $requestCategory = $request->get('category');

            if ($requestCategory) {
                foreach ($requestCategory as $key => $value) {
                    $entryCategory = new EntryCategory();
                    $entryCategory->setEntry($entry)
                        ->setCategory($categoryRepository->findOneBy(['id' => $key]));
                    $em->persist($entryCategory);
                }
            }

            $entry->setType('blog')
                ->setSlug($slugger->slug($form->get('title')->getData()))
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
                'categories' => $category->findBy([], ['order' => 'asc']),
            ]);
    }

    /**
     * @param Request $request
     * @param Entry $entry
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @param EntryCategoryRepository $entryCategoryRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{id}', name: 'app_dashboard_edit_blog', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'entry')]
    public function edit(
        Request                 $request,
        Entry                   $entry,
        EntityManagerInterface  $em,
        UserInterface           $user,
        EntryCategoryRepository $entryCategoryRepository,
        CategoryRepository      $categoryRepository,
    ): Response
    {
        $form = $this->createForm(EntryDetailsType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $requestCategory = $request->get('category');

            if ($requestCategory) {
                $entryCategoryRepository->removeEntryCategory($entry->getId());
                foreach ($requestCategory as $key => $value) {
                    $entryCategory = new EntryCategory();
                    $entryCategory->setEntry($entry)->setCategory($categoryRepository->findOneBy(['id' => $key]));
                    $em->persist($entryCategory);
                }
            }

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
                'categories' => $categoryRepository->findBy([], ['order' => 'asc']),
            ]);
    }
}
