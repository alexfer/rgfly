<?php

namespace App\Controller\Security;

use App\Repository\UserDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Type\User\ProfileType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\{
    Response,
    Request,
};
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use App\Helper\ErrorHandler;

#[Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_USER_USER')")]
class ProfileController extends AbstractController
{

    const PUBLIC_ATTACMENTS_DIR = '/public/usr/picture/';

    /**
     * 
     * @return Response
     */
    #[Route('/profile', name: 'app_profile')]
    public function index(
            Request $request,
            UserInterface $user,
            SluggerInterface $slugger,
            EntityManagerInterface $em,
            UserDetailsRepository $repository,
    ): Response
    {
        $details = $repository->find($user->getId());

        $form = $this->createForm(ProfileType::class, $details);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('picture')->getData();

            if ($file) {
                $fileUploader = new FileUploader($this->getTargetDir($details->getUserId()), $slugger, $em);

                try {
                    $attach = $fileUploader->upload($file)->handle();
                } catch (\Exception $ex) {
                    throw new \Exception($ex->getMessage());
                }

                $details->setAttachId($attach->getId());
            }

            $em->persist($details);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/profile.html.twig', [
                    'errors' => ErrorHandler::handleFormErrors($form),
                    'form' => $form->createView(),
        ]);
    }

    /**
     * 
     * @param int|null $objectId
     * @return string
     */
    private function getTargetDir(?int $objectId): string
    {
        return $this->getParameter('kernel.project_dir') . self::PUBLIC_ATTACMENTS_DIR . $objectId;
    }
}
