<?php

namespace App\Controller\MarketPlace;

use App\Service\MarketPlace\Currency;
use App\Entity\MarketPlace\{Market, MarketCoupon, MarketCouponCode, MarketCouponUsage, MarketCustomer};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
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
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[Route('/{market}/{order}/coupon/{id}', name: 'app_market_place_market_verify_coupon', methods: ['POST'])]
    public function verifyCoupon(
        Request                $request,
        Market                 $market,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): JsonResponse
    {
        // TODO: rewrite with psql function
        $coupon = $em->getRepository(MarketCoupon::class)->getSingleActive($market, MarketCoupon::COUPON_ORDER);

        $payload = $request->getPayload()->all();
        $order = $request->get('order');
        $requestCode = null;

        if ($coupon && $payload && isset($payload['ids'])) {

            $requestCode = implode($payload['ids']);
            $coupons = $em->getRepository(MarketCouponUsage::class)->findBy(['customer' => $user, 'relation' => $order]);

            if (count($coupons)) {
                return $this->json([
                    'success' => false,
                    'message' => $translator->trans('info.text.danger'),
                    ], Response::HTTP_FORBIDDEN);
            }

            $couponCode = $em->getRepository(MarketCouponCode::class)
                ->findOneBy(['coupon' => $em->getRepository(MarketCoupon::class)->find($coupon['id']), 'code' => strtoupper($requestCode)]);

            if (!$couponCode) {
                return $this->json([
                    'success' => false,
                    'message' => $translator->trans('info.text.warning'),
                    ], Response::HTTP_BAD_REQUEST);
            }

            $couponCode->hasUsed(true);
            $em->persist($couponCode);
            $em->flush();

            $customer = $em->getRepository(MarketCustomer::class)->findOneBy(['member' => $user]);

            $couponUsage = new MarketCouponUsage();
            $couponUsage->setCustomer($customer)
                ->setCoupon($em->getRepository(MarketCoupon::class)->find($coupon['id']))
                ->setRelation($order)
                ->setCouponCode($couponCode);



            $em->persist($couponUsage);
            $em->flush();
        }

        $currency = Currency::currency($market->getCurrency());
        $discount = $coupon['price'] ? number_format($coupon['price'], 2, ',', ' ') . $currency['symbol'] : null;

        if ($coupon['discount']) {
            $discount = sprintf("%d%%", $coupon['discount']);
        }

        return $this->json([
            'success' => true,
            'payload' => $requestCode,
            'discount' => $discount,
            'message' => $translator->trans('info.text.success', ['discount' => $discount]),
        ], Response::HTTP_OK);
    }
}
