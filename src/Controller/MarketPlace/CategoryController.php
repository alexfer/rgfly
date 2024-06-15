<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\StoreCategory;
use App\Entity\MarketPlace\StoreCustomer;
use App\Repository\MarketPlace\StoreProductRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/market-place/category')]
class CategoryController extends AbstractController
{
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
    private int $limit = 8;

    /**
     * @param RequestStack $stack
     * @param StoreProductRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(
        RequestStack                             $stack,
        private readonly StoreProductRepository $repository,
        private readonly EntityManagerInterface  $em,
    )
    {
        $this->request = $stack->getCurrentRequest();
        $this->offset = $this->request->get('offset') ?: $this->offset;
        $this->limit = $this->request->get('limit') ?: $this->limit;
    }

    /**
     * @param UserInterface|null $user
     * @return StoreCustomer|null
     */
    protected function customer(?UserInterface $user): ?StoreCustomer
    {
        return $this->em->getRepository(StoreCustomer::class)->findOneBy(['member' => $user]);
    }

    /**
     * @param string $slug
     * @return StoreCategory|null
     */
    protected function category(string $slug): ?StoreCategory
    {
        return $this->em->getRepository(StoreCategory::class)->findOneBy(['slug' => $slug]);
    }

    /**
     * @param UserInterface|null $user
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_market_place_category')]
    public function index(?UserInterface $user): Response
    {
        $products = $this->repository->fetchProducts($this->offset, $this->limit);
        $categories = $this->em->getRepository(StoreCategory::class)->findBy(['parent' => null], ['id' => 'asc']);
        return $this->render('market_place/category/index.html.twig', [
            'parent' => null,
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
            'categories' => $categories,
            'customer' => $this->customer($user),
        ]);
    }

    /**
     * @param UserInterface|null $user
     * @return Response
     * @throws Exception
     */
    #[Route('/{parent}', name: 'app_market_place_parent_category')]
    public function parent(?UserInterface $user): Response
    {
        $slug = $this->request->get('parent');
        $category = $this->category($slug);
        //dd($category);
        $children = $category->getChildren()->toArray();
        $products = $this->repository->findProductsByParentCategory($slug, $this->offset, $this->limit);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'parent' => null,
            'parent_name' => $category->getName(),
            'children' => $children,
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
            'categories' => null,
            'customer' => $this->customer($user),
        ]);
    }

    /**
     * @param UserInterface|null $user
     * @return Response
     * @throws Exception
     */
    #[Route('/{parent}/{child}', name: 'app_market_place_child_category')]
    public function children(?UserInterface $user): Response
    {
        $child = $this->request->get('child');
        $parent = $this->request->get('parent');

        $category = $this->category($child);

        if(!$category) {
            return $this->redirectToRoute('app_market_place_parent_category', ['parent' => $parent]);
        }

        $parent = $this->category($parent);

        $products = $this->repository->findProductsByChildCategory($category->getId(), $this->offset, $this->limit);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'child_name' => $category->getName(),
            'parent_name' => $parent->getName(),
            'parent' => $parent,
            'categories' => null,
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
            'customer' => $this->customer($user),
        ]);
    }
}
