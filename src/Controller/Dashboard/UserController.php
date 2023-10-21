<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\User\ChangePasswordProfileType;
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

    #[Route('/', name: 'app_dashboard_user', methods: ['GET'])]
    public function index(UserRepository $reposiroty): Response
    {
        return $this->render('dashboard/content/user/index.html.twig', [
                    'entries' => $reposiroty->findBy([], ['id' => 'desc']),
        ]);
    }

    #[Route('/details/{id}', name: 'app_dashboard_user_details', methods: ['GET'])]
    public function details(Request $request, UserRepository $reposiroty): Response
    {
        $entry = $reposiroty->find($request->get('id'));
        $country = $entry->getDetails()->getCountry();
        
        $form = $this->createForm(ChangePasswordProfileType::class);
        $form->handleRequest($request);

        return $this->render('dashboard/content/user/details.html.twig', [
                    'entry' => $entry,
                    'form' => $form,
                    'country' => $country ? Countries::getNames(Locale::getDefault())[$country] : null,
        ]);
    }
}
