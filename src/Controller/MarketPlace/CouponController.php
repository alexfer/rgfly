<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Service\MarketPlace\Store\Coupon\Interface\ProcessorInterface as Coupon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/market-place/coupon')]
class CouponController extends AbstractController
{
    /**
     * @param Request $request
     * @param Store $store
     * @param UserInterface $user
     * @param TranslatorInterface $translator
     * @param Coupon $coupon
     * @return JsonResponse
     */
    #[Route('/{store}/{relation}/{id}/{ref}', name: 'app_market_place_market_verify_coupon', methods: ['POST'])]
    public function verifyCoupon(
        Request             $request,
        Store               $store,
        UserInterface       $user,
        TranslatorInterface $translator,
        Coupon              $coupon,
    ): JsonResponse
    {

        $process = $coupon->process($store, $request->get('ref'));

        $payload = $request->getPayload()->all();
        $relation = $request->get('relation');

        if ($process && $payload && isset($payload['ids'])) {

            if ($coupon->getCouponUsage($relation, $user)) {
                return $this->json([
                    'success' => false,
                    'message' => $translator->trans('info.text.danger'),
                ], Response::HTTP_FORBIDDEN);
            }

            $code = implode($payload['ids']);

            if (!$coupon->validate($code)) {
                return $this->json([
                    'success' => false,
                    'message' => $translator->trans('info.text.warning'),
                ], Response::HTTP_BAD_REQUEST);
            }
            $coupon->setInuse($user, $relation, $code);
        }

        $discount = $coupon->discount($store);

        return $this->json([
            'success' => true,
            'discount' => $discount,
            'message' => $translator->trans('info.text.success', ['discount' => $discount]),
        ], Response::HTTP_OK);
    }

}