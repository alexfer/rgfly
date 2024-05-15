<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\{Market, MarketCoupon, MarketProduct};
use App\Form\Type\Dashboard\MarketPlace\CouponType;
use App\Service\Dashboard;
use App\Service\MarketPlace\MarketTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/market-place/coupon')]
class CouponController extends AbstractController
{
    use Dashboard, MarketTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{market}', name: 'app_dashboard_market_place_product_coupon', methods: ['GET'])]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $coupons = $em->getRepository(MarketCoupon::class)->findBy(['market' => $market], ['expired_at' => 'asc'], 25, 0);

        $result = array_map(function ($val) use ($market, $translator) {
            $start = new \DateTime($val->getStartedAt()->format('Y-m-d H:i:s'));
            $end = new \DateTime($val->getExpiredAt()->format('Y-m-d H:i:s'));

            $interval = $end->diff($start);
            $result = null;

            if ($interval->y) {
                $result .= $interval->format("%y years ");
            }
            if ($interval->m) {
                $result .= $interval->format("%m months ");
            }
            if ($interval->d) {
                $result .= $interval->format("%d days ");
            }
            if ($interval->h) {
                $result .= $interval->format("%h hours ");
            }
            if ($interval->i) {
                $result .= $interval->format("%i minutes ");
            }
            if ($interval->s) {
                $result .= $interval->format("%s seconds ");
            }

            if ($val->getStartedAt() > $val->getExpiredAt()) {
                $result = $translator->trans('event.completed');
            }

            return [
                'id' => $val->getId(),
                'name' => $val->getName(),
                'market' => $market->getId(),
                'currency' => $market->getCurrency(),
                'available' => $val->getAvailable(),
                'products' => $val->getProduct()->count(),
                'discount' => $val->getDiscount() ?: 0,
                'price' => $val->getPrice() ?: 0,
                'createdAt' => $val->getCreatedAt(),
                'interval' => $result,
                'expiredAt' => $end,
                'startedAt' => $start,
            ];
        }, $coupons);

        return $this->render('dashboard/content/market_place/coupon/index.html.twig', $this->navbar() + [
                'market' => $market,
                'coupons' => $result,
            ]);
    }

    /**
     * @param Request $request
     * @param Market $market
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/apply/{market}', name: 'app_dashboard_market_place_apply_coupon', methods: ['POST'])]
    public function apply(
        Request                $request,
        Market                 $market,
        TranslatorInterface    $translator,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $coupon = $em->getRepository(MarketCoupon::class)->findOneBy(['market' => $market, 'id' => $payload['id']]);

        foreach ($payload['products'] as $product) {
            $item = $em->getRepository(MarketProduct::class)->find($product);
            $coupon->addProduct($item)->setMarket($market);
            $em->persist($coupon);
        }
        $em->flush();

        return $this->json([
            'success' => true,
            'market' => $market->getId(),
            'message' => $translator->trans('user.entry.updated'),
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/create/{market}', name: 'app_dashboard_market_place_create_coupon', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $coupon = new MarketCoupon();

        $form = $this->createForm(CouponType::class, $coupon);
        $form->handleRequest($request);

        //strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6))

        if ($form->isSubmitted() && $form->isValid()) {
            $coupon->setMarket($market);
            $em->persist($coupon);
            $em->flush();
            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));

            return $this->redirectToRoute('app_dashboard_market_place_edit_coupon', [
                'market' => $request->get('market'),
                'id' => $coupon->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/coupon/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketCoupon $coupon
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{market}/{id}', name: 'app_dashboard_market_place_edit_coupon', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        UserInterface          $user,
        MarketCoupon           $coupon,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $form = $this->createForm(CouponType::class, $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('event')->getData()) {
                $coupon->setExpiredAt(new \DateTimeImmutable('now'));
            }
            $em->persist($coupon);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));

            return $this->redirectToRoute('app_dashboard_market_place_edit_coupon', [
                'market' => $request->get('market'),
                'id' => $coupon->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/coupon/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);

    }
}