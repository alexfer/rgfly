<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\{Store, StoreCoupon, StoreCustomer};
use App\Service\MarketPlace\Store\Coupon\Interface\ProcessorInterface as Coupon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/market-place/store')]
class StoreController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('', name: 'app_market_place_stores')]
    public function index(): Response
    {
        return $this->render('market_place/store/index.html.twig', []);
    }

    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @param Store $store
     * @return Response
     */
    #[Route('/{slug}', name: 'app_market_place_market')]
    public function market(
        Request                $request,
        ?UserInterface         $user,
        EntityManagerInterface $em,
        Store                  $store,
    ): Response
    {
        $customer = $em->getRepository(StoreCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/store/index.html.twig', [
            'store' => $store,
            'customer' => $customer,
        ]);
    }

    /**
     * @param Request $request
     * @param Store $store
     * @param UserInterface $user
     * @param TranslatorInterface $translator
     * @param Coupon $coupon
     * @return JsonResponse
     */
    #[Route('/{store}/{relation}/coupon/{id}/{ref}', name: 'app_market_place_market_verify_coupon', methods: ['POST'])]
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

            if (!$code = $coupon->validate(implode($payload['ids']))) {
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
