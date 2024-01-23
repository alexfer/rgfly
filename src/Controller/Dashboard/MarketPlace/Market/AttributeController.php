<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\MarketProductAttribute;
use App\Entity\MarketPlace\MarketProductAttributeValue;
use App\Form\Type\Dashboard\MarketPlace\AttributeType;
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

#[Route('/dashboard/market-place/attribute')]
class AttributeController extends AbstractController
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
    #[Route('/{market}', name: 'app_dashboard_market_place_market_attribute')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        $attributes = $em->getRepository(MarketProductAttribute::class)->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/attribute/index.html.twig', $this->navbar() + [
                'market' => $market,
                'attributes' => $attributes,
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
    #[Route('/create/{market}', name: 'app_dashboard_market_place_create_attribute', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $attribute = new MarketProductAttribute();
        $form = $this->createForm(AttributeType::class, $attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attributes['colors'] = $form->get('color')->getData();
            $attributes['size'] = $form->get('size')->getData();
            if($attributes['colors']) {
                $attribute->setMarket($market)->setName('color')->setInUse(0)->setInFront(0);

                foreach ($attributes['colors'] as $color) {
                    $attributeValue = new MarketProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($color);
                    $em->persist($value);
                }
                $em->persist($attribute);
            }
            if($attributes['size']) {
                $attribute = new MarketProductAttribute();
                $attribute->setMarket($market)->setName('size')->setInUse(0)->setInFront(0);

                foreach ($attributes['size'] as $size) {
                    $attributeValue = new MarketProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($size);
                    $em->persist($value);
                }
                $em->persist($attribute);
            }
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_market_place_market_attribute');
        }

        return $this->render('dashboard/content/market_place/attribute/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketProductAttribute $attribute
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{market}-{id}', name: 'app_dashboard_market_place_edit_attribute', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        UserInterface          $user,
        MarketProductAttribute $attribute,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        $form = $this->createForm(AttributeType::class, $attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            $attribute->setMarket($market);
//            $em->persist($attribute);
//            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_attribute', [
                'market' => $request->get('market'),
                'id' => $attribute->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/attribute/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketProductAttribute $attribute
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/delete/{market}-{id}', name: 'app_dashboard_delete_attribute', methods: ['POST'])]
    public function delete(
        Request                $request,
        UserInterface          $user,
        MarketProductAttribute $attribute,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        if ($this->isCsrfTokenValid('delete', $request->get('_token'))) {
            $em->remove($attribute);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_market_attribute', ['market' => $market->getId()]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/xhr_create/{market}', name: 'app_dashboard_market_place_xhr_create_attribute', methods: ['POST'])]
    public function xshCreate(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $market = $this->market($request, $user, $em);
        $requestGetPost = $request->get('attribute');
        $responseJson = [];

        if ($requestGetPost && isset($requestGetPost['name'])) {
            if ($this->isCsrfTokenValid('create', $requestGetPost['_token'])) {
                $attribute = new MarketProductAttribute();
                $attribute->setName($requestGetPost['name']);
                $attribute->setMarket($market);
                $em->persist($attribute);
                $em->flush();

                $responseJson = [
                    'json' => [
                        'id' => "#product_manufacturer",
                        'option' => [
                            'id' => $attribute->getId(),
                            'name' => $attribute->getName(),
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