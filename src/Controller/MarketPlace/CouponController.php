<?php declare(strict_types=1);

namespace Inno\Controller\MarketPlace;

use Inno\Entity\MarketPlace\Store;
use Inno\Service\MarketPlace\Store\Coupon\Interface\CouponServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/market-place/coupon')]
class CouponController extends AbstractController
{
    /**
     * @param Request $request
     * @param Store $store
     * @param TranslatorInterface $translator
     * @param CouponServiceInterface $coupon
     * @return JsonResponse
     */
    #[Route('/{store}/{relation}/{id}/{ref}', name: 'app_market_place_market_verify_coupon', methods: ['POST'])]
    public function verifyCoupon(
        Request                $request,
        Store                  $store,
        TranslatorInterface    $translator,
        CouponServiceInterface $coupon,
    ): JsonResponse
    {
        $user = $this->getUser();

        $process = $coupon->process($store, $request->get('ref'));

        $payload = $request->getPayload()->all();
        $relation = $request->get('relation');

        if ($process && $payload && isset($payload['ids'])) {

            if ($coupon->getCouponUsage((int)$relation, $user)) {
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
            $coupon->setInuse($user, (int)$relation, $code);
        }

        $discount = $coupon->discount($store);

        return $this->json([
            'success' => true,
            'discount' => $discount,
            'message' => $translator->trans('info.text.success', ['discount' => $discount]),
        ], Response::HTTP_OK);
    }

}