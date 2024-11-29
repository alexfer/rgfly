<?php declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Participant;
use App\Entity\User;
use App\Entity\UserDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class PublishController extends AbstractController
{
    /**
     * @var array
     */
    private array $payload = [];

    /**
     * @param HubInterface $hub
     */
    public function __construct(private readonly HubInterface $hub)
    {
    }

    /**
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/dashboard/hub/index', name: 'app_dashboard_publish_index', methods: ['GET'])]
    public function index(EntityManagerInterface $manager): Response
    {
        $conversations = $manager->getRepository(Conversation::class)->findAll();
        $users = $manager->getRepository(UserDetails::class)->findByNot($this->getUser());

        return $this->render('dashboard/broadcast/index.html.twig', [
            'conversations' => $conversations,
            'users' => $users,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/dashboard/hub/message/{hub}', name: 'app_dashboard_publish_index_messages', methods: ['GET'])]
    public function conversation(Request $request, EntityManagerInterface $manager): Response
    {
        $conversation = $manager->getRepository(Conversation::class)->findOneBy(['hub' => $request->get('hub')]);
//        $participant = $manager->getRepository(Participant::class)->findOneBy(['conversation' => $conversation]);
//
//        if ($participant->getOwner()->getId() == $this->getUser()->getId()) {
//            $manager->getRepository(Message::class)->setRead($conversation);
//        }

        $messages = $manager->getRepository(Message::class)->findBy(['conversation' => $conversation], ['created_at' => 'ASC']);

        return $this->render('dashboard/broadcast/conversation.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    #[Route('/dashboard/hub/reply', name: 'app_dashboard_publish_reply', methods: ['POST'])]
    public function reply(
        Request                $request,
        EntityManagerInterface $manager,
    ): JsonResponse
    {
        $this->payload = $request->getPayload()->all();

        $conversation = $manager->getRepository(Conversation::class)->findOneBy(['hub' => $this->payload['hub']]);

        $message = new Message();
        $message->setBody($this->payload['message']);
        $message->setRead(false);
        $message->setSender($this->getUser());
        $conversation->addMessage($message);

        $participant = new Participant();
        $participant->setOwner($manager->getRepository(User::class)->find($this->payload['recipient']));
        $conversation->addParticipant($participant);

        $manager->persist($message);
        $manager->persist($participant);
        $manager->flush();

        return $this->publish($participant, $message);

    }

    /**
     * @param Participant $participant
     * @param Message $message
     * @return JsonResponse
     */
    private function publish(Participant $participant, Message $message): JsonResponse
    {
        $createdAt = $message->getcreatedAt()->format('F j, H:i');

        $update = new Update(
            '/hub/' . $this->payload['recipient'],
            json_encode(['update' => [
                'createdAt' => $createdAt,
                'sender' => "{$this->getUser()->getUserDetails()->getFirstName()}  {$this->getUser()->getUserDetails()->getLastName()}",
                'count' => $participant->getOwner()->getParticipants()->count(),
                'message' => $this->payload['message'],
            ]]),
        );

        $this->hub->publish($update);

        return $this->json(['message' => "Sent to  {$participant->getOwner()->getUserDetails()->getFirstName()}  at  {$createdAt}"]);
    }
}
