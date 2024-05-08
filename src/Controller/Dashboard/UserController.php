<?php

namespace App\Controller\Dashboard;

use App\Entity\User;
use App\Form\Type\User\ChangePasswordProfileType;
use App\Repository\{AttachRepository, UserDetailsRepository, UserRepository,};
use App\Service\Dashboard;
use App\Service\FileUploader;
use App\Service\Interface\ImageValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{Request, Response,};
use Symfony\Component\Intl\{Countries, Locale,};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/user')]
class UserController extends AbstractController
{
    use Dashboard;

    /**
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @return Response
     */
    #[Route('', name: 'app_dashboard_user', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserInterface  $user,
    ): Response
    {
        $query = $request->query->get('search');
        $users = $em->getRepository(User::class)->fetch($query);
        return $this->render('dashboard/content/user/index.html.twig', $this->navbar() + ['users' => $users]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @param UserDetailsRepository $repository
     * @param SluggerInterface $slugger
     * @param CacheManager $cacheManager
     * @param ParameterBagInterface $params
     * @param AttachRepository $attachRepository
     * @param ImageValidatorInterface $imageValidator
     * @return Response
     * @throws Exception
     */
    #[Route('/details/{id}/change-picture', name: 'app_dashboard_change_picture_user', methods: ['POST'])]
    public function changePicture(
        Request                 $request,
        TranslatorInterface     $translator,
        EntityManagerInterface  $em,
        UserDetailsRepository   $repository,
        SluggerInterface        $slugger,
        CacheManager            $cacheManager,
        ParameterBagInterface   $params,
        AttachRepository        $attachRepository,
        ImageValidatorInterface $imageValidator,
    ): Response
    {
        $user = $repository->find($request->get('id'));
        $file = $request->files->get('file');

        if ($file) {

            $validate = $imageValidator->validate($file, $translator);

            if ($validate->has(0)) {
                return $this->json(['message' => $validate->get(0)->getMessage(), 'picture' => null]);
            }

            $fileUploader = new FileUploader($this->getTargetDir($user->getId(), $params), $slugger, $em);

            try {
                $attach = $fileUploader->upload($file)->handle($user);
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }

            $attachments = $attachRepository->findAll();
            foreach ($attachments as $attachment) {
                $user->removeAttach($attachment);
            }

            $user->getUser()->setAttach($attach);
            $user->addAttach($attach);
        }

        $em->persist($user);
        $em->flush();

        $url = "storage/user/picture/{$request->get('id')}/{$attach->getName()}";
        $picture = $cacheManager->getBrowserPath(parse_url($url, PHP_URL_PATH), 'user_preview', [], null);

        return $this->json(['message' => $translator->trans('user.picture.changed'), 'picture' => $picture]);
    }

    /**
     *
     * @param Request $request
     * @param UserRepository $repository
     * @param UserInterface $user
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/details/{id}/{tab}', name: 'app_dashboard_details_user', methods: ['GET', 'POST'])]
    public function details(
        Request                     $request,
        UserRepository              $repository,
        UserInterface               $user,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $em,
        TranslatorInterface         $translator,
    ): Response
    {
        $entry = $repository->find($request->get('id'));
        $country = $entry->getUserDetails()->getCountry();

        $form = $this->createForm(ChangePasswordProfileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $encodedPassword = $passwordHasher->hashPassword(
                $entry,
                $form->get('plainPassword')->getData()
            );

            $entry->setPassword($encodedPassword);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.password.changed')]));

            return $this->redirectToRoute('app_dashboard_details_user', ['id' => $entry->getId(), 'tab' => 'security']);
        }

        return $this->render('dashboard/content/user/details.html.twig', $this->navbar() + [
                'entry' => $entry,
                'form' => $form,
                'country' => $country ? Countries::getNames(Locale::getDefault())[$country] : null,
            ]);
    }

    /**
     *
     * @param int|null $id
     * @param ParameterBagInterface $params
     * @return string
     */
    private function getTargetDir(?int $id, ParameterBagInterface $params): string
    {
        return sprintf('%s/picture/', $params->get('user_storage_dir')) . $id;
    }
}
