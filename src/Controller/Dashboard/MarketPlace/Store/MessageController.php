<?php

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\{Store, StoreMessage};
use App\Service\MarketPlace\Dashboard\Store\Interface\ServeStoreInterface as StoreInterface;
use App\Service\MarketPlace\StoreTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/marker-place/message')]
class MessageController extends AbstractController
{
    use StoreTrait;

    /**
     * @param UserInterface $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_dashboard_market_place_message_stores')]
    public function index(
        UserInterface          $user,
        Request                $request,
        EntityManagerInterface $manager,
    ): Response
    {
        $stores = $manager->getRepository(Store::class)->stores($user);

        return $this->render('dashboard/content/market_place/message/stores.html.twig', [
            'stores' => $stores['result'],
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param StoreInterface $serveStore
     * @return Response
     * @throws Exception
     */
    #[Route('/{store}', name: 'app_dashboard_market_place_message_current')]
    public function current(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        StoreInterface         $serveStore,
    ): Response
    {
        $store = $this->store($serveStore, $user);
        $messages = $em->getRepository(StoreMessage::class)->fetchAll($store, 'low', 0, 20);

        $pagination = $this->paginator->paginate(
            $messages['data'] !== null ? $messages['data'] : [],
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        return $this->render('dashboard/content/market_place/message/index.html.twig', [
            'messages' => $pagination,
        ]);
    }

    #[Route('/{store}/{id}', name: 'app_dashboard_market_place_message_conversation')]
    public function conversation(
        Request                $request,
        UserInterface          $user,
        StoreInterface         $serveStore,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($serveStore, $user);
        $repository = $em->getRepository(StoreMessage::class);

        $message = $repository->findOneBy(['store' => $store, 'id' => $request->get('id')]);
        $conversation = $repository->findBy(['store' => $store, 'parent' => $message->getId()]);

        return $this->render('dashboard/content/market_place/message/conversation.html.twig', [
            'message' => $message,
            'conversation' => $conversation,
        ]);
    }
}