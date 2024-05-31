<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreProduct;
use App\Entity\MarketPlace\StoreWishlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place/wishlist')]
class WishlistController extends AbstractController
{
    /**
     * @param Request $request
     * @param StoreProduct $product
     * @param EntityManagerInterface $em
     * @param UserInterface|null $user
     * @return Response
     */
    #[Route('/add/{slug}', name: 'app_market_place_add_wishlist', methods: ['POST'])]
    public function add(
        Request                $request,
        StoreProduct          $product,
        EntityManagerInterface $em,
        ?UserInterface         $user,
    ): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $customer = $em->getRepository(StoreCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        $responseStatus = Response::HTTP_OK;

        if (!$customer) {
            $customer = null;
            $responseStatus = Response::HTTP_UNAUTHORIZED;
        }

        $market = $em->getRepository(Store::class)->find($parameters['market']);

        if ($market && $customer) {
            $wishlist = new StoreWishlist();
            $wishlist->setStore($market)
                ->setCustomer($customer)
                ->setProduct($product);

            $em->persist($wishlist);
            $em->flush();
        }

        return $this->json([
            'product' => $product->getSlug(),
            'market' => $parameters['market'],
        ], $responseStatus);
    }
}