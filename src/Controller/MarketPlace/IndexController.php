<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\StoreCustomer;
use App\Repository\MarketPlace\StoreProductRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place')]
class IndexController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @param StoreProductRepository $repository
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_market_place_index')]
    public function index(
        Request                 $request,
        ?UserInterface          $user,
        EntityManagerInterface  $em,
        StoreProductRepository $repository,
    ): Response
    {
        $offset = $request->get('offset') ?: 0;
        $limit = $request->get('limit') ?: 8;

        $products = $repository->fetchProducts($offset, $limit);

        $customer = $em->getRepository(StoreCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/index.html.twig', [
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
            'customer' => $customer,
        ]);
    }
}
