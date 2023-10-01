<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_USER_USER')")]
class ProfileController extends AbstractController
{

    /**
     * 
     * @return Response
     */
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/profile.html.twig', []);
    }
}
