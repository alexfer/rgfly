<?php

namespace App\Controller\Dashboard;

use App\Entity\{Entry, EntryAttachment, EntryCategory, EntryDetails};
use App\Form\Type\Dashboard\EntryDetailsType;
use App\Repository\{CategoryRepository, EntryAttachmentRepository, EntryCategoryRepository, EntryRepository,};
use App\Repository\AttachRepository;
use App\Service\Dashboard;
use App\Service\FileUploader;
use App\Service\Interface\ImageValidatorInterface;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/blog')]
class BlogController extends AbstractController
{

    use Dashboard;

    const array CHILDREN = [
        'blog' => [
            'menu.dashboard.overview.blog' => 'app_dashboard_blog',
            'menu.dashboard.create.blog' => 'app_dashboard_create_blog',
        ],
    ];

    /**
     * @param int|null $id
     * @param ParameterBagInterface $params
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

        return $this->render('dashboard/content/blog/index.html.twig', $this->navbar() + [
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
            } catch (UniqueConstraintViolationException $e) {
                $error = $translator->trans('slug.unique', [
                    '%name%' => $translator->trans('label.form.title'),
                    '%value%' => $title,
                ], 'validators');
            }
        }

        if ($form->isSubmitted() && $form->isValid() && !$error) {

            $requestCategory = $request->get('category');

            if ($requestCategory) {
                foreach ($requestCategory as $key => $value) {
                    $entryCategory = new EntryCategory();
                    $entryCategory->setEntry($entry)
                        ->setCategory($category->findOneBy(['id' => $key]));
                    $em->persist($entryCategory);
                }
            }

            $details = new EntryDetails();

            $details->setTitle($title)
                ->setShortContent($form->get('short_content')->getData())
                ->setContent($form->get('content')->getData())
                ->setEntry($entry);

            $em->persist($details);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));

            return $this->redirectToRoute('app_dashboard_edit_blog', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/blog/_form.html.twig', $this->navbar() + [
                'form' => $form,
                'error' => $error,
                'entry' => $entry,
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
     * @param SluggerInterface $slugger
     * @return Response
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
        SluggerInterface        $slugger,
    ): Response
    {
        $form = $this->createForm(EntryDetailsType::class, $entry);
        $form->handleRequest($request);

        $error = null;
        $title = $form->get('title')->getData();

        if ($title) {
            try {
                $entry->setType('blog')->setSlug($slugger->slug($title)->lower());
                $em->persist($entry);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $error = $translator->trans('slug.unique', [
                    '%name%' => $translator->trans('label.form.title'),
                    '%value%' => $title,
                ], 'validators');
            }
        }

        if ($form->isSubmitted() && $form->isValid() && !$error) {

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
                ->setSlug($slugger->slug($title)->lower())
                ->setUpdatedAt(new DateTime())
                ->setDeletedAt($form->get('status')->getData() == 'trashed' ? new DateTime() : null);

            $em->persist($entry);

            $details = $entry->getEntryDetails()
                ->setTitle($title)
                ->setShortContent($form->get('short_content')->getData())
                ->setContent($form->get('content')->getData())
                ->setEntry($entry);

            $em->persist($details);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));

            return $this->redirectToRoute('app_dashboard_edit_blog', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/blog/_form.html.twig', $this->navbar() + [
                'form' => $form,
                'entry' => $entry,
                'error' => $error,
                'categories' => $categoryRepository->findBy([], ['position' => 'asc']),
            ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntryRepository $repository
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @param CacheManager $cacheManager
     * @param ParameterBagInterface $params
     * @param EntryAttachmentRepository $entryAttachmentRepository
     * @param ImageValidatorInterface $imageValidator
     * @return Response
     * @throws Exception
     */
    #[Route('/attach/{id}', name: 'app_dashboard_blog_attach', methods: ['POST'])]
    public function attach(
        Request                   $request,
        TranslatorInterface       $translator,
        EntryRepository           $repository,
        EntityManagerInterface    $em,
        SluggerInterface          $slugger,
        CacheManager              $cacheManager,
        ParameterBagInterface     $params,
        EntryAttachmentRepository $entryAttachmentRepository,
        ImageValidatorInterface   $imageValidator,
    ): Response
    {
        $file = $request->files->get('file');

        $id = $request->get('id');
        $entry = $repository->findOneBy(['id' => $id]);
        $detailsId = $entry->getEntryDetails()->getId();

        if ($file) {

            $validate = $imageValidator->validate($file, $translator);

            if ($validate->has(0)) {
                return $this->json([
                    'message' => $validate->get(0)->getMessage(),
                    'picture' => null,
                ]);
            }

            $fileUploader = new FileUploader($this->getTargetDir($detailsId, $params), $slugger, $em);

            try {
                $attach = $fileUploader->upload($file)->handle();
            } catch (Exception $ex) {
                return $this->json([
                    'success' => false,
                    'message' => $ex->getMessage(),
                    'picture' => null,
                ]);
            }

            $attachments = $entryAttachmentRepository->findAll();
            foreach ($attachments as $attachment) {
                $attachment->getInUse(0);
            }
            unset($attachment);

            $entryAttachmentRepository->resetStatus($entry->getEntryDetails());

            $entryAttachment = new EntryAttachment();
            $entryAttachment->setDetails($entry)
                ->setAttach($attach)
                ->setInUse(1);

            $em->persist($entryAttachment);
            $em->flush();
        }

        $storage = $params->get('entry_storage_picture');

        $url = "{$storage}/{$detailsId}/{$attach->getName()}";
        $picture = $cacheManager->getBrowserPath(parse_url($url, PHP_URL_PATH), 'entry_preview');

        return $this->json([
            'success' => true,
            'message' => $translator->trans('entry.picture.appended'),
            'picture' => $picture,
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntryRepository $entryRepository
     * @param EntityManagerInterface $em
     * @param EntryAttachmentRepository $entryAttachmentRepository
     * @param CacheManager $cacheManager
     * @param AttachRepository $attachRepository
     * @param ParameterBagInterface $params
     * @return Response
     */
    #[Route('/attach-remove/{entry}', name: 'app_dashboard_blog_attach_remove', methods: ['POST'])]
    public function remove(
        Request                   $request,
        TranslatorInterface       $translator,
        EntryRepository           $entryRepository,
        EntityManagerInterface    $em,
        EntryAttachmentRepository $entryAttachmentRepository,
        CacheManager              $cacheManager,
        AttachRepository          $attachRepository,
        ParameterBagInterface     $params
    ): Response
    {
        $entry = $entryRepository->find($request->get('entry'));
        $details = $entry->getEntryDetails();

        $attachments = $entryAttachmentRepository->findAll();
        foreach ($attachments as $attachment) {
            $attach = $attachment->getAttach();
            $fs = new Filesystem();
            $oldFile = $this->getTargetDir($details->getId(), $params) . '/' . $attach->getName();

            if ($cacheManager->isStored($oldFile, 'entry_preview')) {
                $cacheManager->remove($oldFile, 'entry_preview');
            }

            if ($cacheManager->isStored($oldFile, 'entry_view')) {
                $cacheManager->remove($oldFile, 'entry_view');
            }

            if ($fs->exists($oldFile)) {
                $fs->remove($oldFile);
            }

            $attachment->setInUse(0)->setAttach(null)->setDetails(null);
            $em->persist($attachment);
            $em->remove($attachment);
            $em->remove($attach);
            $em->flush();
        }

        return $this->json([
            'success' => true,
            'message' => $translator->trans('entry.picture.remove'),
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntryRepository $entryRepository
     * @param EntityManagerInterface $em
     * @param EntryAttachmentRepository $entryAttachmentRepository
     * @param AttachRepository $attachRepository
     * @return Response
     */
    #[Route('/attach-set-use/{entry}', name: 'app_dashboard_blog_attach_set_use', methods: ['POST'])]
    public function setInUse(
        Request                   $request,
        TranslatorInterface       $translator,
        EntryRepository           $entryRepository,
        EntityManagerInterface    $em,
        EntryAttachmentRepository $entryAttachmentRepository,
        AttachRepository          $attachRepository,
    ): Response
    {
        $entry = $entryRepository->find($request->get('entry'));
        $details = $entry->getEntryDetails();

        $attachments = $entryAttachmentRepository->findAll();
        foreach ($attachments as $attachment) {
            $attachment->setInUse(0);
        }

        $parameters = json_decode($request->getContent(), true);
        $attach = $attachRepository->find($parameters['id']);

        $entryAttachment = $entryAttachmentRepository->findOneBy(['details' => $details->getEntry(), 'attach' => $attach]);

        $entryAttachment->setInUse(1);
        $em->persist($entryAttachment);
        $em->flush();

        return $this->json([
            'success' => true,
            'message' => $translator->trans('entry.picture.default'),
        ]);
    }
}
