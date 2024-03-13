<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\MarketBrand;
use App\Form\Type\Dashboard\MarketPlace\BrandType;
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

#[Route('/dashboard/market-place/brand')]
class BrandController extends AbstractController
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
    #[Route('/{market}', name: 'app_dashboard_market_place_market_brand')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $brands = $em->getRepository(MarketBrand::class)->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/brand/index.html.twig', $this->navbar() + [
                'market' => $market,
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
    #[Route('/create/{market}', name: 'app_dashboard_market_place_create_brand', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $brand = new MarketBrand();

        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand->setMarket($market);
            $em->persist($brand);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_brand', [
                'market' => $request->get('market'),
                'id' => $brand->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/brand/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketBrand $brand
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{market}-{id}', name: 'app_dashboard_market_place_edit_brand', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        UserInterface          $user,
        MarketBrand            $brand,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brand->setMarket($market);
            $em->persist($brand);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_brand', [
                'market' => $request->get('market'),
                'id' => $brand->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/brand/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketBrand $brand
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete/{market}-{id}', name: 'app_dashboard_delete_brand', methods: ['POST'])]
    public function delete(
        Request                $request,
        UserInterface          $user,
        MarketBrand            $brand,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        if ($this->isCsrfTokenValid('delete', $request->get('_token')) && !$brand->getMarketProductBrands()->count()) {
            $em->remove($brand);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_market_brand', ['market' => $market->getId()]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/xhr_create/{market}', name: 'app_dashboard_market_place_xhr_create_brand', methods: ['POST'])]
    public function xshCreate(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $market = $this->market($request, $user, $em);
        $requestGetPost = $request->get('brand');
        $responseJson = [];

        if ($requestGetPost && isset($requestGetPost['name'])) {
            if ($this->isCsrfTokenValid('create', $requestGetPost['_token'])) {
                $brand = new MarketBrand();
                $brand->setName($requestGetPost['name']);
                $brand->setMarket($market);
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