<?php

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\StoreMessage;
use App\Service\Dashboard;
use App\Service\MarketPlace\StoreTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/marker-place/message')]
class MessageController extends AbstractController
{
    use Dashboard, StoreTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{store}', name: 'app_dashboard_market_place_market_message')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $messages = $em->getRepository(StoreMessage::class)->fetchAll($store, 'low', 0, 20);
        //$messages['data'] = array_reverse($messages['data']);

        return $this->render('dashboard/content/market_place/message/index.html.twig', [
                'messages' => $messages['data'],
            ]);
    }
}