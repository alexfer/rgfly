<?php

namespace App\Controller\Dashboard;

use App\Entity\{Entry, EntryAttachment, EntryCategory, EntryDetails};
use App\Form\Type\Dashboard\EntryDetailsType;
use App\Repository\CategoryRepository;
use App\Repository\EntryCategoryRepository;
use App\Repository\EntryRepository;
use App\Service\FileUploader;
use App\Service\Navbar;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{Request, Response,};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     *
     * @param int|null $id
     * @return string
     */
    private function getTargetDir(?int $id, ParameterBagInterface $params): string
    {
        $storage = sprintf('%s/picture/', $params->get('entry_storage_dir'));
        return $storage . $id;
    }

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
     * @param SluggerInterface $slugger
     * @param TranslatorInterface $translator
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
        TranslatorInterface    $translator,
    ): Response
    {
        $entry = new Entry();

        $form = $this->createForm(EntryDetailsType::class, $entry);
        $form->handleRequest($request);

        $error = null;
        $title = $form->get('title')->getData();

        if ($title) {
            try {
                $entry->setType('blog')
                    ->setSlug($slugger->slug($title)->lower())
                    ->setUser($user);
                $em->persist($entry);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $error = $translator->trans('slug.unique', [], 'validators');
            }
        }

        if ($form->isSubmitted() && $form->isValid() && !$error) {

            $requestCategory = $request->get('category');

            if ($requestCategory) {
                foreach ($requestCategory as $key => $value) {
                    $entryCategory = new EntryCategory();
                    $entryCategory->setEntry($entry)
                        ->setCategory($categoryRepository->findOneBy(['id' => $key]));
                    $em->persist($entryCategory);
                }
            }

            $details = new EntryDetails();

            $details->setTitle($form->get('title')->getData())
                ->setShortContent($form->get('short_content')->getData())
                ->setContent($form->get('content')->getData())
                ->setEntry($entry);

            $em->persist($details);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));

            return $this->redirectToRoute('app_dashboard_edit_blog', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/blog/_form.html.twig', $this->build($user) + [
                'form' => $form,
                'error' => $error,
                'categories' => $category->findBy([], ['position' => 'asc']),
            ]);
    }

    /**
     * @param Request $request
     * @param Entry $entry
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @param EntryCategoryRepository $entryCategoryRepository
     * @param CategoryRepository $categoryRepository
     * @param TranslatorInterface $translator
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
        TranslatorInterface     $translator,
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

            $details = $entry->getEntryDetails()
                ->setTitle($form->get('title')->getData())
                ->setShortContent($form->get('short_content')->getData())
                ->setContent($form->get('content')->getData())
                ->setEntry($entry);

            $em->persist($details);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));

            return $this->redirectToRoute('app_dashboard_edit_blog', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/blog/_form.html.twig', $this->build($user) + [
                'form' => $form,
                'entry' => $entry,
                'error' => null,
                'categories' => $categoryRepository->findBy([], ['position' => 'asc']),
            ]);
    }

    #[Route('/attach/{id}', name: 'app_dashboard_blog_attch', methods: ['GET', 'POST'])]
    public function attach(
        Request                $request,
        TranslatorInterface    $translator,
        EntryRepository        $repository,
        EntityManagerInterface $em,
        SluggerInterface       $slugger,
        CacheManager           $cacheManager,
        ParameterBagInterface  $params,
    ): Response
    {
        $file = $request->files->get('file');

        $id = $request->get('id');
        $entry = $repository->findOneBy(['id' => $id]);

        if ($file) {

            $fileUploader = new FileUploader($this->getTargetDir($entry->getEntryDetails()->getId(), $params), $slugger, $em);

            try {
                $attach = $fileUploader->upload($file)->handle();
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }

            $entryAttachment = new EntryAttachment();
            $entryAttachment->setDetails($entry)->setAttach($attach);

            $em->persist($entryAttachment);
            $em->flush();
        }

        $url = "entry/picture/{$id}/{$attach->getName()}";
        $picture = $cacheManager->getBrowserPath(parse_url($url, PHP_URL_PATH), 'entry_preview', [], null);

        return $this->json(['message' => $translator->trans('entry.picture.appended'), 'picture' => $picture]);
    }

}
