<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\MarketManufacturer;
use App\Form\Type\Dashboard\MarketPlace\ManufacturerType;
use App\Service\Dashboard;
use App\Service\MarketPlace\MarketTrait;
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
    use Dashboard, MarketTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{market}', name: 'app_dashboard_market_place_market_manufacturer')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $manufacturers = $em->getRepository(MarketManufacturer::class)->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/manufacturer/index.html.twig', $this->navbar() + [
                'market' => $market,
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
    #[Route('/create/{market}', name: 'app_dashboard_market_place_create_manufacturer', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $manufacturer = new MarketManufacturer();

        $form = $this->createForm(ManufacturerType::class, $manufacturer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manufacturer->setMarket($market);
            $em->persist($manufacturer);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_manufacturer', [
                'market' => $request->get('market'),
                'id' => $manufacturer->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/manufacturer/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketManufacturer $manufacturer
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{market}-{id}', name: 'app_dashboard_market_place_edit_manufacturer', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        UserInterface          $user,
        MarketManufacturer     $manufacturer,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        $form = $this->createForm(ManufacturerType::class, $manufacturer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manufacturer->setMarket($market);
            $em->persist($manufacturer);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_manufacturer', [
                'market' => $request->get('market'),
                'id' => $manufacturer->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/manufacturer/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketManufacturer $manufacturer
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete/{market}-{id}', name: 'app_dashboard_delete_manufacturer', methods: ['POST'])]
    public function delete(
        Request                $request,
        UserInterface          $user,
        MarketManufacturer     $manufacturer,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        if ($this->isCsrfTokenValid('delete', $request->get('_token')) && !$manufacturer->getMarketProductManufacturers()->count()) {
            $em->remove($manufacturer);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_market_manufacturer', ['market' => $market->getId()]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/xhr_create/{market}', name: 'app_dashboard_market_place_xhr_create_manufacturer', methods: ['POST'])]
    public function xshCreate(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $market = $this->market($request, $user, $em);
        $requestGetPost = $request->get('manufacturer');
        $responseJson = [];

        if ($requestGetPost && isset($requestGetPost['name'])) {
            if ($this->isCsrfTokenValid('create', $requestGetPost['_token'])) {
                $manufacturer = new MarketManufacturer();
                $manufacturer->setName($requestGetPost['name']);
                $manufacturer->setMarket($market);
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