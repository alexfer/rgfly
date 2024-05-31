<?php

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\{Store, StoreCoupon, StoreCouponCode, StoreProduct};
use App\Form\Type\Dashboard\MarketPlace\CouponType;
use App\Service\Dashboard;
use App\Service\MarketPlace\StoreTrait;
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
    use Dashboard, StoreTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    #[Route('/{store}', name: 'app_dashboard_market_place_product_coupon', methods: ['GET'])]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $coupons = $em->getRepository(StoreCoupon::class)->findBy(['store' => $store], ['expired_at' => 'asc'], 25, 0);

        $result = array_map(function ($val) use ($store, $translator) {
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

            if (time() > $val->getExpiredAt()->getTimestamp()) {
                $result = $translator->trans('event.completed');
            }

            return [
                'id' => $val->getId(),
                'name' => $val->getName(),
                'market' => $store->getId(),
                'currency' => $store->getCurrency(),
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

        return $this->render('dashboard/content/market_place/coupon/index.html.twig',  [
                'store' => $store,
                'coupons' => $result,
            ]);
    }

    /**
     * @param Request $request
     * @param Store $store
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/apply/{store}', name: 'app_dashboard_market_place_apply_coupon', methods: ['POST'])]
    public function apply(
        Request                $request,
        Store                  $store,
        TranslatorInterface    $translator,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $payload = $request->getPayload()->all();
        $coupon = $em->getRepository(StoreCoupon::class)->findOneBy(['store' => $store, 'id' => $payload['id']]);

        foreach ($payload['products'] as $product) {
            $item = $em->getRepository(StoreProduct::class)->find($product);
            $coupon->addProduct($item)->setStore($store);
            $em->persist($coupon);
        }
        $em->flush();

        return $this->json([
            'success' => true,
            'store' => $store->getId(),
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
    #[Route('/create/{store}', name: 'app_dashboard_market_place_create_coupon', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $coupon = new StoreCoupon();

        $form = $this->createForm(CouponType::class, $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coupon->setStore($store);

            $em->persist($coupon);

            $available = $form->get('available')->getData();

            for ($i = 0; $i < $available; $i++) {
                $code = new StoreCouponCode();
                $code->setCoupon($coupon)
                    ->setCode(strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6)));
                $em->persist($code);
            }

            $em->flush();
            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));

            return $this->redirectToRoute('app_dashboard_market_place_edit_coupon', [
                'store' => $request->get('store'),
                'id' => $coupon->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/coupon/_form.html.twig',  [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param StoreCoupon $coupon
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{store}/{id}', name: 'app_dashboard_market_place_edit_coupon', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        UserInterface          $user,
        StoreCoupon            $coupon,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $store = $this->store($request, $user, $em);
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
                'market' => $store->getId(),
                'id' => $coupon->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/coupon/_form.html.twig',  [
                'form' => $form,
            ]);

    }

    /**
     * @param Request $request
     * @param StoreCoupon $coupon
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete/{store}/{id}', name: 'app_dashboard_market_place_delete_coupon', methods: ['POST'])]
    public function delete(
        Request                $request,
        StoreCoupon            $coupon,
        ?UserInterface         $user,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $token = $request->get('_token');

        if (!$token) {
            $content = $request->getPayload()->all();
            $token = $content['_token'];
        }

        if ($this->isCsrfTokenValid('delete', $token)) {

            $code = $em->getRepository(StoreCouponCode::class)->find($coupon->getId());

            if ($code) {
                $em->remove($code);
            }

            $em->remove($coupon);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_market_brand', ['market' => $store->getId()]);
    }
}