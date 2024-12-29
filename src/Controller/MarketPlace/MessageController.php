<?php declare(strict_types=1);

namespace Essence\Controller\MarketPlace;

use Essence\Service\MarketPlace\Store\Message\Interface\MessageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/market-place/message')]
class MessageController extends AbstractController
{
    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param MessageServiceInterface $message
     * @return JsonResponse
     */
    #[Route('', name: 'market_place_obtain_message')]
    public function obtain(
        Request                 $request,
        TranslatorInterface     $translator,
        MessageServiceInterface $message,

    ): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'success' => false,
                'error' => $translator->trans('store.message.unauthorized'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        $payload = $request->getPayload()->all();

        $exclude = [
            'product' => !empty($payload['product']),
            'order' => !empty($payload['order']),
        ];

        $errors = $message->process($payload, $user, $exclude);

        if (count($errors)) {
            return $this->json($errors, $errors['code']);
        }

        return $message->obtainAndResponse($user);
    }
}