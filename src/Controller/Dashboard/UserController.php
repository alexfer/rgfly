<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\User\ChangePasswordProfileType;
use App\Helper\ErrorHandler;
use Symfony\Component\HttpFoundation\{
    Response,
    Request,
};
use Symfony\Component\Intl\{
    Locale,
    Countries,
};
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

#[Route('/dashboard/user')]
class UserController extends AbstractController
{

    /**
     * 
     * @param UserRepository $reposiroty
     * @return Response
     */
    #[Route('/', name: 'app_dashboard_user', methods: ['GET'])]
    public function index(UserRepository $reposiroty): Response
    {
        return $this->render('dashboard/content/user/index.html.twig', [
                    'entries' => $reposiroty->findBy([], ['id' => 'desc']),
        ]);
    }

    /**
     * 
     * @param Request $request
     * @param UserRepository $reposiroty
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/details/{id}/{tab}', name: 'app_dashboard_user_details', methods: ['GET', 'POST'])]
    public function details(
            Request $request,
            UserRepository $reposiroty,
            UserPasswordHasherInterface $passwordHasher,
            EntityManagerInterface $em,
            TranslatorInterface $translator,
    ): Response
    {
        $entry = $reposiroty->find($request->get('id'));
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

            return $this->redirectToRoute('app_dashboard_user_details', ['id' => $entry->getId(), 'tab' => 'security']);
        }

        return $this->render('dashboard/content/user/details.html.twig', [
                    'entry' => $entry,
                    'form' => $form,
                    'errors' => ErrorHandler::handleFormErrors($form),
                    'country' => $country ? Countries::getNames(Locale::getDefault())[$country] : null,
        ]);
    }
}
