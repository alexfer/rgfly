<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
     * @param ValidatorInterface $validator
     * @return Response
     */
    #[Route('/details/{id}/{tab}', name: 'app_dashboard_user_details', methods: ['GET', 'POST'])]
    public function details(
            Request $request,
            UserRepository $reposiroty,
            ValidatorInterface $validator,
    ): Response
    {
        $entry = $reposiroty->find($request->get('id'));
        $country = $entry->getDetails()->getCountry();

        $form = $this->createForm(ChangePasswordProfileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->addFlash('success', json_encode(['message' => 'Password has been changed successfully.']));
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
