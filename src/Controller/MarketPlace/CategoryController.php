<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketCustomer;
use App\Repository\MarketPlace\MarketProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place/category')]
class CategoryController extends AbstractController
{
    /**
     * @param MarketProductRepository $marketProductRepository
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('', name: 'app_market_place_category')]
    public function index(
        MarketProductRepository $marketProductRepository,
        ?UserInterface          $user,
        EntityManagerInterface  $em,
    ): Response
    {
        // TODO: Replace with psql function - get_products
        $products = $marketProductRepository->getProducts(8);
        shuffle($products);

        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/category/index.html.twig', [
            'products' => $products,
            'customer' => $customer,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserInterface|null $user
     * @param MarketProductRepository $marketProductRepository
     * @return Response
     */
    #[Route('/{parent}', name: 'app_market_place_parent_category')]
    public function parent(
        Request                 $request,
        EntityManagerInterface  $em,
        ?UserInterface          $user,
        MarketProductRepository $marketProductRepository,
    ): Response
    {
        $category = $em->getRepository(MarketCategory::class)
            ->findOneBy([
                'slug' => $request->get('parent'),
            ]);

        $categories = [];
        $children = $category->getChildren()->toArray();

        foreach ($children as $child) {
            $categories[$child->getId()] = $child->getName();
        }
        $products = $marketProductRepository->findProductsByParentCategory(array_keys($categories));

        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'children' => $children,
            'products' => $products,
            'customer' => $customer,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserInterface|null $user
     * @param MarketProductRepository $marketProductRepository
     * @return Response
     */
    #[Route('/{parent}/{child}', name: 'app_market_place_child_category')]
    public function children(
        Request                 $request,
        EntityManagerInterface  $em,
        ?UserInterface          $user,
        MarketProductRepository $marketProductRepository,
    ): Response
    {
        $category = $em->getRepository(MarketCategory::class)
            ->findOneBy([
                'slug' => $request->get('child'),
            ]);

        // TODO: Replace with psql function - get_product
        $products = $marketProductRepository->findProductsByChildrenCategory($category->getId());

        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'products' => $products,
            'customer' => $customer,
        ]);
    }
}
