<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\{Market, MarketCustomer};
use App\Service\MarketPlace\Market\Coupon\Interface\ProcessorInterface as Coupon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/market-place/market')]
class MarketController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('', name: 'app_market_place_markets')]
    public function index(): Response
    {
        return $this->render('market_place/market/index.html.twig', []);
    }

    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @param Market $market
     * @return Response
     */
    #[Route('/{slug}', name: 'app_market_place_market')]
    public function market(
        Request                $request,
        ?UserInterface         $user,
        EntityManagerInterface $em,
        Market                 $market,
    ): Response
    {
        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/market/index.html.twig', [
            'market' => $market,
            'customer' => $customer,
        ]);
    }

    /**
     * @param Request $request
     * @param Market $market
     * @param UserInterface $user
     * @param TranslatorInterface $translator
     * @param Coupon $coupon
     * @return JsonResponse
     */
    #[Route('/{market}/{order}/coupon/{id}', name: 'app_market_place_market_verify_coupon', methods: ['POST'])]
    public function verifyCoupon(
        Request             $request,
        Market              $market,
        UserInterface       $user,
        TranslatorInterface $translator,
        Coupon              $coupon,
    ): JsonResponse
    {

        $process = $coupon->process($market);

        $payload = $request->getPayload()->all();
        $order = $request->get('order');

        if ($process && $payload && isset($payload['ids'])) {

            if ($coupon->getCouponUsage($order, $user)) {
                return $this->json([
                    'success' => false,
                    'message' => $translator->trans('info.text.danger'),
                ], Response::HTTP_FORBIDDEN);
            }

            if (!$code = $coupon->validate(implode($payload['ids']))) {
                return $this->json([
                    'success' => false,
                    'message' => $translator->trans('info.text.warning'),
                ], Response::HTTP_BAD_REQUEST);
            }

            $coupon->setInuse($user, $order, $code);
        }

        $discount = $coupon->discount($market);

        return $this->json([
            'success' => true,
            'discount' => $discount,
            'message' => $translator->trans('info.text.success', ['discount' => $discount]),
        ], Response::HTTP_OK);
    }
}
