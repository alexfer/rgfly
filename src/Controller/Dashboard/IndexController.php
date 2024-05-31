<?php

namespace App\Controller\Dashboard;

use App\Entity\MarketPlace\Store;
use App\Service\Dashboard;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard')]
class IndexController extends AbstractController
{

    use Dashboard;

    /**
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('', name: 'app_dashboard')]
    public function index(
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $stores = $em->getRepository(Store::class)->findBy($this->criteria($user, null, 'owner'));
        return $this->render('dashboard/content/index.html.twig',  [
                'stores' => $stores,
            ]);
    }
}
