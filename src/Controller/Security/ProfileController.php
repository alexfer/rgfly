<?php

namespace App\Controller\Security;

use App\Form\Type\User\ProfileType;
use App\Repository\AttachRepository;
use App\Repository\UserDetailsRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{Request, Response,};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{

    /**
     *
     * @var string|null
     */
    private ?string $storage;

    public function __construct(ParameterBagInterface $params)
    {
        $this->storage = sprintf('%s/picture/', $params->get('user_storage_dir'));
    }

    private function getRepository(
        Request               $request,
        UserDetailsRepository $repository,
    ): object
    {
        return $repository->find($request->get('id'));
    }

    private function getFile(Request $request, string $key): string
    {
        return $request->files->get($key);
    }

    /**
     * @param Request $request
     * @param UserRepository $repository
     * @param TranslatorInterface $translator
     * @param AttachRepository $attach
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route('/profile/attach/setup', name: 'app_profile_attach_setup', methods: ['POST'])]
    public function setup(
        Request                $request,
        UserRepository         $repository,
        TranslatorInterface    $translator,
        AttachRepository       $attach,
        EntityManagerInterface $em,
    ): Response
    {
        try {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
        } catch (NotFoundExceptionInterface $ex) {
            throw new Exception($ex);
        }

        $attachment = $attach->find($request->get('id'));
        $owner = $repository->find($user->getId());
        $owner->setAttach($attachment);

        $em->persist($owner);
        $em->flush();

        return $this->json(['message' => $translator->trans('user.picture.setup')]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @param UserDetailsRepository $repository
     * @param UserInterface $user
     * @param SluggerInterface $slugger
     * @param CacheManager $cacheManager
     * @param ParameterBagInterface $params
     * @return Response
     * @throws Exception
     */
    #[Route('/profile/attach', name: 'app_profile_attach', methods: ['POST'])]
    public function attach(
        Request                $request,
        TranslatorInterface    $translator,
        EntityManagerInterface $em,
        UserDetailsRepository  $repository,
        UserInterface          $user,
        SluggerInterface       $slugger,
        CacheManager           $cacheManager,
        ParameterBagInterface  $params,
    ): Response
    {
        $details = $repository->find($user->getId());
        $file = $request->files->get('file');

        if ($file) {
            $fileUploader = new FileUploader($this->getTargetDir($user->getId(), $params), $slugger, $em);

            try {
                $attach = $fileUploader->upload($file)->handle($details);
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
            $details->getUser()->setAttach($attach);
            $details->addAttach($attach);
        }

        $em->persist($details);
        $em->flush();

        $url = "user/picture/{$user->getId()}/{$attach->getName()}";
        $picture = $cacheManager->getBrowserPath(parse_url($url, PHP_URL_PATH), 'user_thumb', [], null);

        return $this->json(['message' => $translator->trans('user.picture.changed'), 'picture' => $picture]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $em
     * @param UserDetailsRepository $repository
     * @return Response
     * @throws Exception
     */
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(
        Request                $request,
        UserInterface          $user,
        SluggerInterface       $slugger,
        EntityManagerInterface $em,
        UserDetailsRepository  $repository,
    ): Response
    {
        $details = $repository->find($user->getId());

        $form = $this->createForm(ProfileType::class, $details);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('picture')->getData();

            if ($file) {
                $fileUploader = new FileUploader($this->getTargetDir($user->getId()), $slugger, $em);

                try {
                    $attach = $fileUploader->upload($file)->handle($details);
                } catch (Exception $ex) {
                    throw new Exception($ex->getMessage());
                }

                $details->getUser()->setAttach($attach);
                $details->addAttach($attach);
            }
            $details->setFirstName($form->get('first_name')->getData());
            $details->setLastName($form->get('last_name')->getData());

            $em->persist($details);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/profile.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     *
     * @param int|null $id
     * @return string
     */
    private function getTargetDir(?int $id): string
    {
        return $this->storage . $id;
    }
}
