<?php

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\StoreManufacturer;
use App\Form\Type\Dashboard\MarketPlace\ManufacturerType;
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

#[Route('/dashboard/market-place/manufacturer')]
class ManufacturerController extends AbstractController
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
    #[Route('/{store}', name: 'app_dashboard_market_place_store_manufacturer')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $manufacturers = $em->getRepository(StoreManufacturer::class)->manufacturers($store, $request->query->get('search'));

        return $this->render('dashboard/content/market_place/manufacturer/index.html.twig',  [
                'store' => $store,
                'manufacturers' => $manufacturers,
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
    #[Route('/create/{store}', name: 'app_dashboard_market_place_create_manufacturer', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $manufacturer = new StoreManufacturer();

        $form = $this->createForm(ManufacturerType::class, $manufacturer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manufacturer->setStore($store);
            $em->persist($manufacturer);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_manufacturer', [
                'store' => $request->get('store'),
                'id' => $manufacturer->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/manufacturer/_form.html.twig',  [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param StoreManufacturer $manufacturer
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{store}/{id}', name: 'app_dashboard_market_place_edit_manufacturer', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        UserInterface          $user,
        StoreManufacturer     $manufacturer,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $store = $this->store($request, $user, $em);

        $form = $this->createForm(ManufacturerType::class, $manufacturer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manufacturer->setStore($store);
            $em->persist($manufacturer);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_manufacturer', [
                'store' => $request->get('store'),
                'id' => $manufacturer->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/manufacturer/_form.html.twig',  [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param StoreManufacturer $manufacturer
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete/{store}/{id}', name: 'app_dashboard_delete_manufacturer', methods: ['POST'])]
    public function delete(
        Request                $request,
        UserInterface          $user,
        StoreManufacturer     $manufacturer,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $token = $request->get('_token');

        if (!$token) {
            $content = $request->getPayload()->all();
            $token = $content['_token'];
        }

        if ($this->isCsrfTokenValid('delete', $token) && !$manufacturer->getStoreProductManufacturers()->count()) {
            $em->remove($manufacturer);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_store_manufacturer', ['store' => $store->getId()]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/xhr_create/{store}', name: 'app_dashboard_market_place_xhr_create_manufacturer', methods: ['POST'])]
    public function xshCreate(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $store = $this->store($request, $user, $em);
        $requestGetPost = $request->get('manufacturer');
        $responseJson = [];

        if ($requestGetPost && isset($requestGetPost['name'])) {
            if ($this->isCsrfTokenValid('create', $requestGetPost['_token'])) {
                $manufacturer = new StoreManufacturer();
                $manufacturer->setName($requestGetPost['name']);
                $manufacturer->setStore($store);
                $em->persist($manufacturer);
                $em->flush();

                $responseJson = [
                    'json' => [
                        'id' => "#product_manufacturer",
                        'option' => [
                            'id' => $manufacturer->getId(),
                            'name' => $manufacturer->getName(),
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