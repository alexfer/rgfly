<?php

namespace App\Controller\MarketPlace;

use App\Service\MarketPlace\Store\Message\Interface\ProcessorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/market-place/message')]
class MessageController extends AbstractController
{
    #[Route('', name: 'market_place_obtain_message')]
    public function obtain(
        Request             $request,
        ?UserInterface      $user,
        TranslatorInterface $translator,
        ProcessorInterface  $message,

    ): JsonResponse
    {
        if (!$user) {
            return $this->json([
                'success' => false,
                'error' => $translator->trans('market.message.unauthorized'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        $payload = $request->getPayload()->all();

        $exclude = [
            'product' => !empty($payload['product']),
            'order' => !empty($payload['order']),
        ];

        $errors = $message->process($payload, $exclude);

        if (count($errors)) {
            return $this->json($errors, $errors['code']);
        }

        return $message->obtainAndResponse($user);
    }
}