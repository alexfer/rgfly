<?php

namespace App\Controller\Dashboard;

use App\Entity\MarketPlace\Store;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard')]
class IndexController extends AbstractController
{

    /**
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_dashboard')]
    public function index(
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $stores = $em->getRepository(Store::class)->stores($user);
        return $this->render('dashboard/content/index.html.twig',  [
                'stores' => $stores,
            ]);
    }
}
