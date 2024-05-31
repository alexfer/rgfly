<?php

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\StoreBrand;
use App\Form\Type\Dashboard\MarketPlace\BrandType;
use App\Service\Dashboard;
use App\Service\MarketPlace\StoreTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/market-place/brand')]
class BrandController extends AbstractController
{
    use Dashboard, StoreTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{store}', name: 'app_dashboard_market_place_store_brand')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $brands = $em->getRepository(StoreBrand::class)->brands($store, $request->query->get('search'));

        return $this->render('dashboard/content/market_place/brand/index.html.twig', [
            'store' => $store,
            'brands' => $brands,
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
    #[Route('/create/{store}', name: 'app_dashboard_market_place_create_brand', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $brand = new StoreBrand();

        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand->setStore($store);
            $em->persist($brand);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_brand', [
                'store' => $request->get('store'),
                'id' => $brand->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/brand/_form.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param StoreBrand $brand
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{store}/{id}', name: 'app_dashboard_market_place_edit_brand', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        UserInterface          $user,
        StoreBrand             $brand,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $store = $this->store($request, $user, $em);

        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brand->setStore($store);
            $em->persist($brand);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_brand', [
                'store' => $request->get('store'),
                'id' => $brand->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/brand/_form.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param StoreBrand $brand
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete/{store}/{id}', name: 'app_dashboard_delete_brand', methods: ['POST'])]
    public function delete(
        Request                $request,
        UserInterface          $user,
        StoreBrand            $brand,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $token = $request->get('_token');

        if (!$token) {
            $content = $request->getPayload()->all();
            $token = $content['_token'];
        }

        if ($this->isCsrfTokenValid('delete', $token) && !$brand->getStoreProductBrands()->count()) {
            $em->remove($brand);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_market_brand', ['store' => $store->getId()]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/xhr_create/{store}', name: 'app_dashboard_market_place_xhr_create_brand', methods: ['POST'])]
    public function xshCreate(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $store = $this->store($request, $user, $em);
        $requestGetPost = $request->get('brand');
        $responseJson = [];

        if ($requestGetPost && isset($requestGetPost['name'])) {
            if ($this->isCsrfTokenValid('create', $requestGetPost['_token'])) {
                $brand = new StoreBrand();
                $brand->setName($requestGetPost['name']);
                $brand->setStore($store);
                $em->persist($brand);
                $em->flush();

                $responseJson = [
                    'json' => [
                        'id' => "#product_brand",
                        'option' => [
                            'id' => $brand->getId(),
                            'name' => $brand->getName(),
                        ],
                    ],
                ];
            }
        }
        $response = new JsonResponse($responseJson);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}