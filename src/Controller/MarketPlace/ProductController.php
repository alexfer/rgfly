<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\{StoreCustomer, StoreProduct};
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place/product')]
class ProductController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('/{slug}/{tab}', name: 'app_market_place_product')]
    public function index(
        Request                $request,
        ?UserInterface         $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $em->getRepository(StoreCustomer::class)
            ->findOneBy(['member' => $user]);

        $product = $em->getRepository(StoreProduct::class)
            ->fetchProduct($request->get('slug'));

        if ($product['product'] === null) {
            throw $this->createNotFoundException();
        }
        //dd($product['product']);
        return $this->render('market_place/product/index.html.twig', [
            'product' => $product['product'],
            'customer' => $customer,
        ]);
    }
}
