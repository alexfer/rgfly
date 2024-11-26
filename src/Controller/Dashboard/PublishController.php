<?php declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Participant;
use App\Entity\User;
use App\Entity\UserDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class PublishController extends AbstractController
{

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
     * @param HubInterface $hub
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/dashboard/hub/publish', name: 'app_dashboard_publish_publish', methods: ['POST'])]
    public function publish(
        HubInterface           $hub,
        Request                $request,
        EntityManagerInterface $manager,
    ): JsonResponse
    {
        $payload = $request->getPayload()->all();

        $user = $manager->getRepository(User::class)->find($payload['id']);
        $owner = $manager->getRepository(UserDetails::class)->findOneBy(['user' => $this->getUser()]);
        $user = $manager->getRepository(UserDetails::class)->findOneBy(['user' => $user]);

        $createdAt = new DatePoint(timezone: new \DateTimezone('UTC'));

        $conversation = new Conversation();
        $conversation->setHub(Uuid::v4());

        $message = new Message();
        //$message->setConversation($conversation);
        $message->setBody($payload['message']);
        $message->setRead(false);
        $message->setSender($owner->getUser());
        $conversation->addMessage($message);

        $participant = new Participant();
        //$participant->setConversation($conversation);
        $participant->setOwner($user->getUser());
        $conversation->addParticipant($participant);

        $manager->persist($conversation);
        $manager->persist($message);
        $manager->persist($participant);
        $manager->flush();

        $update = new Update(
            '/hub/' . $user->getId(),
            json_encode(['update' => [
                'createdAt' => $createdAt->format('m F, D. Y, H:i'),
                'sender' => $owner->getFirstName() . ' ' . $owner->getLastName(),
                'message' => $payload['message'],
            ]]),
        );

        $hub->publish($update);

        return $this->json(['message' => 'Sent to ' . $user->getFirstName() . ' at ' . $createdAt->format('m F, D. Y, H:i')]);
    }
}
