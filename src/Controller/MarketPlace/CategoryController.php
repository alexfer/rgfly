<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketCategoryProduct;
use App\Entity\MarketPlace\MarketProduct;
use App\Repository\MarketPlace\MarketCategoryRepository;
use App\Repository\MarketPlace\MarketProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/category')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'app_market_place_category')]
    public function index(EntityManagerInterface $em): Response
    {
        // TODO: Replace with psql function - get_product
        $products = $em->getRepository(MarketProduct::class)->findBy(['deleted_at' => null], null, 8);
        shuffle($products);

        return $this->render('market_place/category/index.html.twig', []);
    }

    #[Route('/{parent}', name: 'app_market_place_parent_category')]
    public function parent(
        Request                  $request,
        EntityManagerInterface   $em,
        MarketProductRepository $marketProductRepository,
    ): Response
    {
        $category = $em->getRepository(MarketCategory::class)->findOneBy([
            'slug' => $request->get('parent'),
        ]);

        $categories = [];
        $children = $category->getChildren()->toArray();

        foreach ($children as $child) {
            $categories[$child->getId()] = $child->getName();
        }
        $products = $marketProductRepository->findProductsByParentCategory(array_keys($categories));
        //dd($products);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'children' => $children,
            'products' => $products,
        ]);
    }

    #[Route('/{parent}/children', name: 'app_market_place_children_category')]
    public function children(
        Request                $request,
        EntityManagerInterface $em,
    ): Response
    {

        // TODO: Replace with psql function - get_product
        $products = $em->getRepository(MarketProduct::class)->findBy(['deleted_at' => null], null, 8);
        shuffle($products);

        return $this->render('market_place/category/index.html.twig', []);
    }
}
