<?php

namespace App\Controller\Security;

use App\Form\Type\User\ProfileType;
use App\Helper\ErrorHandler;
use App\Repository\UserDetailsRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{Request, Response,};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_USER_USER')")]
class ProfileController extends AbstractController
{

    const PUBLIC_USER_PICTURE_DIR = '/public/user/picture/';

    /**
     *
     * @var string|null
     */
    private ?string $storage;

    public function __construct(ParameterBagInterface $params)
    {
        $this->storage = sprintf('%s/picture/', $params->get('user_storage_dir'));
    }

    /**
     *
     * @return Response
     */
    #[Route('/profile', name: 'app_profile')]
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

                $details->addAttach($attach);
            }

            $em->persist($details);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/profile.html.twig', [
            'errors' => ErrorHandler::handleFormErrors($form),
            'user' => $details,
            'form' => $form->createView(),
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
