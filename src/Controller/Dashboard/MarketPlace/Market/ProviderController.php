<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketProvider;
use App\Form\Type\Dashboard\MarketPlace\ProductType;
use App\Form\Type\Dashboard\MarketPlace\ProviderType;
use App\Service\MarketPlace\MarketTrait;
use App\Service\Navbar;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/market-place/provider')]
class ProviderController extends AbstractController
{
    use Navbar, MarketTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{market}', name: 'app_dashboard_market_place_market_provider')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $criteria = $this->criteria($user, ['id' => $request->get('market')], 'owner');
        // TODO: check in future
        $market = $em->getRepository(Market::class)->findOneBy($criteria, ['id' => 'desc']);
        $providers = $em->getRepository(MarketProvider::class)->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/provider/index.html.twig', $this->build($user) + [
                'market' => $market,
                'providers' => $providers,
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
    #[Route('/create/{market}', name: 'app_dashboard_market_place_create_provider', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $provider = new MarketProvider();

        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $provider->setMarket($market);
            $em->persist($provider);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_provider', [
                'market' => $request->get('market'),
                'id' => $provider->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/provider/_form.html.twig', $this->build($user) + [
                'form' => $form,
            ]);
    }

    #[Route('/edit/{market}/{id}', name: 'app_dashboard_market_place_edit_provider', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        UserInterface          $user,
        MarketProvider         $provider,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($provider);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_provider', [
                'market' => $request->get('market'),
                'id' => $provider->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/provider/_form.html.twig', $this->build($user) + [
                'form' => $form,
            ]);
    }
}