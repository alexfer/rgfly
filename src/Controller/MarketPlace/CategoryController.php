<?php

declare(strict_types=1);

namespace App\Controller\MarketPlace;

use App\Controller\Trait\ControllerTrait;
use App\Entity\MarketPlace\{StoreCategory, StoreProduct};
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, RequestStack, Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place/category')]
class CategoryController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var Request|null
     */
    private ?Request $request;

    /**
     * @var int
     */
    private int $offset = 0;

    /**
     * @var int
     */
    private int $limit = 30;

    /**
     * @param RequestStack $stack
     */
    public function __construct(RequestStack $stack)
    {
        $this->request = $stack->getCurrentRequest();
        $page = is_numeric($this->request->query->get('page')) ?: null;

        if (isset($page)) {
            $this->offset = $this->limit * ($page - 1);
        }
    }


    /**
     * @param string $slug
     * @return StoreCategory
     */
    private function category(string $slug): StoreCategory
    {
        $category = $this->em->getRepository(StoreCategory::class)
            ->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException();
        }
        return $category;
    }

    /**
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_market_place_category')]
    public function index(): Response
    {
        $products = $this->em->getRepository(StoreProduct::class)
            ->fetchProducts($this->offset, $this->limit);

        $categories = $this->em->getRepository(StoreCategory::class)
            ->findBy(['parent' => null], ['id' => 'asc']);

        return $this->render('market_place/category/index.html.twig', [
            'parent' => null,
            'products' => $products['data'],
            'pages' => ceil($products['rows_count'] / $this->limit),
            'categories' => $categories,
            'customer' => $this->getCustomer($this->getUser()),
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     */
    #[Route('/{parent}', name: 'app_market_place_parent_category')]
    public function parent(): Response
    {
        $slug = $this->request->get('parent');

        try {
            $category = $this->category($slug);
        } catch (\Exception $_) {
            return $this->redirectToRoute('app_market_place_category');
        }

        $children = $category->getChildren()->toArray();

        $products = $this->em->getRepository(StoreProduct::class)
            ->findProductsByParentCategory($slug, $this->offset, $this->limit);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'parent' => null,
            'parent_name' => $category->getName(),
            'children' => $children,
            'products' => $products['data'],
            'pages' => ceil($products['rows_count'] / $this->limit),
            'categories' => null,
            'customer' => $this->getCustomer($this->getUser()),
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     */
    #[Route('/{parent}/{child}', name: 'app_market_place_child_category')]
    public function children(): Response
    {
        $child = $this->request->get('child');
        $parent = $this->request->get('parent');
        $redirectTo = $this->redirectToRoute('app_market_place_parent_category', ['parent' => $parent]);

        try {
            $category = $this->category($child);
        } catch (\Exception $_) {
            return $redirectTo;
        }

        try {
            $parent = $this->category($parent);
        } catch (\Exception $_) {
            return $redirectTo;
        }

        $products = $this->em->getRepository(StoreProduct::class)
            ->findProductsByChildCategory($category->getId(), $this->offset, $this->limit);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'child_name' => $category->getName(),
            'parent_name' => $parent->getName(),
            'parent' => $parent,
            'categories' => null,
            'products' => $products['data'],
            'pages' => ceil($products['rows_count'] / $this->limit),
            'customer' => $this->getCustomer($this->getUser()),
        ]);
    }
}
