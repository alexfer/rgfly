<?php declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Entity\Conversation;
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

class PublishController extends AbstractController
{

    #[Route('/dashboard/hub/index', name: 'app_dashboard_publish_index', methods: ['GET'])]
    public function index(EntityManagerInterface $manager): Response
    {
        $conversations = $manager->getRepository(Conversation::class)->findAll();
        $users = $manager->getRepository(UserDetails::class)->findAll();

        return $this->render('dashboard/broadcast/index.html.twig', [
            'conversations' => $conversations,
            'users' => $users,
        ]);
    }

    /**
     * @throws \DateMalformedStringException
     */
    #[Route('/dashboard/hub/publish', name: 'app_dashboard_publish_publish', methods: ['GET'])]
    public function publish(
        HubInterface           $hub,
        Request                $request,
        EntityManagerInterface $manager,
    ): JsonResponse
    {
        $currentUser = $this->getUser();

        $user = $manager->getRepository(UserDetails::class)->findOneBy(['user' => $currentUser]);

        $createdAt = new DatePoint(timezone: new \DateTimezone('UTC'));
        $payload = $request->getPayload();

        $update = new Update(
            '/hub',
            json_encode(['update' => [
                'createdAt' => $createdAt->format('m F, D. Y, H:i'),
                'sender' => $user->getFirstName() . ' ' . $user->getLastName(),
                'payload' => $payload,
            ]]),
        );

        $hub->publish($update);

        return $this->json(['message' => 'Sent to ' . $user->getFirstName() . ' at ' . $createdAt->format('m F, D. Y, H:i')]);
    }
}
