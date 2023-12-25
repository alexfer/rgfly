<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketManufacturer;
use App\Entity\MarketPlace\MarketProvider;
use App\Entity\MarketPlace\MarketSupplier;
use App\Service\Navbar;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard/market-place/manufacturer')]
class ManufacturerController extends AbstractController
{
    use Navbar;

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
        $criteria = $this->criteria($user, ['id' => $request->get('market')], 'owner');
        // TODO: check in future
        $market = $em->getRepository(Market::class)->findOneBy($criteria, ['id' => 'desc']);
        $manufacturers = $em->getRepository(MarketManufacturer::class)->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/manufacturer/index.html.twig', $this->build($user) + [
                'market' => $market,
                'manufacturers' => $manufacturers,
            ]);
    }
}