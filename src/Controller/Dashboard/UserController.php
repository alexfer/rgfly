<?php

namespace App\Controller\Dashboard;

use App\Form\Type\User\ChangePasswordProfileType;
use App\Helper\ErrorHandler;
use App\Repository\{OldUserDetailsRepository, UserRepository,};
use App\Service\Dashboard;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{Request, Response,};
use Symfony\Component\Intl\{Countries, Locale,};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/user')]
class UserController extends AbstractController
{

    use Dashboard;

    /**
     *
     * @param UserRepository $repository
     * @param UserInterface $user
     * @return Response
     */
    #[Route('', name: 'app_dashboard_user', methods: ['GET'])]
    public function index(
        UserRepository $repository,
        UserInterface  $user,
    ): Response
    {
        return $this->render('dashboard/content/user/index.html.twig', $this->build($user) + [
                'entries' => $repository->findBy([], ['id' => 'desc']),
            ]);
    }

    /**
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @param UserRepository $repository
     * @param SluggerInterface $slugger
     * @return Response
     * @throws \Exception
     */
    #[Route('/details/{id}/change-picture', name: 'app_dashboard_change_picture_user', methods: ['POST'])]
    public function changePicture(
        Request                  $request,
        TranslatorInterface      $translator,
        EntityManagerInterface   $em,
        OldUserDetailsRepository $repository,
        SluggerInterface         $slugger,
        CacheManager             $cacheManager,
        ParameterBagInterface    $params,
    ): Response
    {
        $entry = $repository->find($request->get('id'));
        $file = $request->files->get('file');

        if ($file) {
            $fileUploader = new FileUploader($this->getTargetDir($entry->getId(), $params), $slugger, $em);

            try {
                $attach = $fileUploader->upload($file)->handle($entry);
            } catch (\Exception $ex) {
                throw new \Exception($ex->getMessage());
            }

            $entry->setAttachId($attach->getId());
        }

        $em->persist($entry);
        $em->flush();
        $url = sprintf('user/picture/%d/%s', $request->get('id'), $attach->getName());
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
        $country = $entry->getDetails()->getCountry();

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

        return $this->render('dashboard/content/user/details.html.twig', $this->build($user) + [
                'entry' => $entry,
                'form' => $form,
                'errors' => ErrorHandler::handleFormErrors($form),
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
