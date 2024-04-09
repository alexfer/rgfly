<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketCustomer;
use App\Repository\MarketPlace\MarketProductRepository;
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
     * @param MarketProductRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(
        RequestStack                             $stack,
        private readonly MarketProductRepository $repository,
        private readonly EntityManagerInterface  $em,
    )
    {
        $this->request = $stack->getCurrentRequest();
        $this->offset = $this->request->get('offset') ?: $this->offset;
        $this->limit = $this->request->get('limit') ?: $this->limit;
    }

    /**
     * @param UserInterface|null $user
     * @return MarketCustomer|null
     */
    protected function customer(?UserInterface $user): ?MarketCustomer
    {
        return $this->em->getRepository(MarketCustomer::class)->findOneBy(['member' => $user]);
    }

    /**
     * @param string $slug
     * @return MarketCategory|null
     */
    protected function category(string $slug): ?MarketCategory
    {
        return $this->em->getRepository(MarketCategory::class)->findOneBy(['slug' => $slug]);
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
        $category = $this->em->getRepository(MarketCategory::class)->findOneBy(['parent' => null], ['id' => 'asc']);

        return $this->render('market_place/category/index.html.twig', [
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
            'category' => $category,
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
        $children = $category->getChildren()->toArray();
        $products = $this->repository->findProductsByParentCategory($slug, $this->offset, $this->limit);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'parent' => null,
            'children' => $children,
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
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
        $parent = $this->category($parent);

        $products = $this->repository->findProductsByChildCategory($category->getId(), $this->offset, $this->limit);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'parent' => $parent,
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
            'customer' => $this->customer($user),
        ]);
    }
}
