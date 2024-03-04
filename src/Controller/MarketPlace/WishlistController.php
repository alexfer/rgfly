<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketProduct;
use App\Entity\MarketPlace\MarketWishlist;
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
     * @param MarketProduct $product
     * @param EntityManagerInterface $em
     * @param UserInterface|null $user
     * @return Response
     */
    #[Route('/add/{slug}', name: 'app_market_place_add_wishlist', methods: ['POST'])]
    public function add(
        Request                $request,
        MarketProduct          $product,
        EntityManagerInterface $em,
        ?UserInterface         $user,
    ): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        $responseStatus = Response::HTTP_OK;

        if (!$customer) {
            $customer = null;
            $responseStatus = Response::HTTP_UNAUTHORIZED;
        }

        $market = $em->getRepository(Market::class)->find($parameters['market']);

        if ($market && $customer) {
            $wishlist = new MarketWishlist();
            $wishlist->setMarket($market)
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