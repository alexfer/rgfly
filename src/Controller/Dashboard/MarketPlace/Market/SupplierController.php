<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\MarketSupplier;
use App\Form\Type\Dashboard\MarketPlace\SupplerType;
use App\Service\MarketPlace\MarketTrait;
use App\Service\Dashboard;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/market-place/supplier')]
class SupplierController extends AbstractController
{
    use Dashboard, MarketTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{market}', name: 'app_dashboard_market_place_market_supplier')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $suppliers = $em->getRepository(MarketSupplier::class)->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/supplier/index.html.twig', $this->navbar() + [
                'market' => $market,
                'suppliers' => $suppliers,
                'countries' => Countries::getNames(Locale::getDefault()),
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
    #[Route('/create/{market}', name: 'app_dashboard_market_place_create_supplier', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $supplier = new MarketSupplier();

        $form = $this->createForm(SupplerType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $supplier->setMarket($market);
            $em->persist($supplier);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_supplier', [
                'market' => $request->get('market'),
                'id' => $supplier->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/supplier/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketSupplier $supplier
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{market}-{id}', name: 'app_dashboard_market_place_edit_supplier', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        UserInterface          $user,
        MarketSupplier         $supplier,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        $form = $this->createForm(SupplerType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $supplier->setMarket($market);
            $em->persist($supplier);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_supplier', [
                'market' => $request->get('market'),
                'id' => $supplier->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/supplier/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketSupplier $supplier
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete/{market}-{id}', name: 'app_dashboard_delete_supplier', methods: ['POST'])]
    public function delete(
        Request                $request,
        UserInterface          $user,
        MarketSupplier         $supplier,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        if ($this->isCsrfTokenValid('delete', $request->get('_token'))) {
            $em->remove($supplier);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_market_supplier', ['market' => $market->getId()]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/xhr-create/{market}', name: 'app_dashboard_market_place_xhr_create_supplier', methods: ['POST'])]
    public function xshCreate(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $market = $this->market($request, $user, $em);
        $requestGetPost = $request->request->all();
        $responseJson = [];

        if ($requestGetPost && isset($requestGetPost['supplier']['name'])) {
            if ($this->isCsrfTokenValid('create', $requestGetPost['supplier']['_token'])) {
                $supplier = new MarketSupplier();
                $supplier->setName($requestGetPost['supplier']['name']);
                $supplier->setCountry($requestGetPost['supplier']['country']);
                $supplier->setMarket($market);
                $em->persist($supplier);
                $em->flush();

                $responseJson = [
                    'json' => [
                        'id' => "#product_supplier",
                        'option' => [
                            'id' => $supplier->getId(),
                            'name' => $supplier->getName(),
                        ],
                    ],
                ];
            }
        }
        $response = new JsonResponse($responseJson);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/xhr-load-countries/{market}', name: 'app_dashboard_market_place_xhr_load_countries', methods: ['GET'])]
    public function xhrLoadCounties(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $market = $this->market($request, $user, $em);

        $countries = [
            'market' => $market->getName(),
            'countries' => Countries::getNames(Locale::getDefault())
        ];

        return new JsonResponse($countries);
    }

}