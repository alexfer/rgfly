<?php

namespace App\Controller\Dashboard;

use App\Entity\MarketPlace\Market;
use App\Service\Navbar;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Redis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard')]
class IndexController extends AbstractController
{

    use Navbar;

    /**
     * @param UserInterface $user
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
        $markets = $em->getRepository(Market::class)->findBy($this->criteria($user, null, 'owner'));
        return $this->render('dashboard/content/index.html.twig', $this->build($user) + [
                'markets' => $markets,
            ]);
    }
}
