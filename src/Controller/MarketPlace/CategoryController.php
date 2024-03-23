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
    private Request $request;

    private int $offset = 0;

    private int $limit = 8;

    public function __construct(RequestStack $stack)
    {
        $this->request = $stack->getCurrentRequest();
        $this->offset = $this->request->get('offset') ?: $this->offset;
        $this->limit = $this->request->get('limit') ?: $this->limit;
    }

    /**
     * @param Request $request
     * @param MarketProductRepository $marketProductRepository
     * @param UserInterface|null $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_market_place_category')]
    public function index(
        Request                 $request,
        MarketProductRepository $marketProductRepository,
        ?UserInterface          $user,
        EntityManagerInterface  $em,
    ): Response
    {
        $products = $marketProductRepository->fetchProducts($this->offset, $this->limit);
        $category = $em->getRepository(MarketCategory::class)->findOneBy(['parent' => null], ['id' => 'asc']);

        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/category/index.html.twig', [
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
            'category' => $category,
            'customer' => $customer,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserInterface|null $user
     * @param MarketProductRepository $marketProductRepository
     * @return Response
     * @throws Exception
     */
    #[Route('/{parent}', name: 'app_market_place_parent_category')]
    public function parent(
        Request                 $request,
        EntityManagerInterface  $em,
        ?UserInterface          $user,
        MarketProductRepository $marketProductRepository,
    ): Response
    {
        $slug = $request->get('parent');
        $category = $em->getRepository(MarketCategory::class)
            ->findOneBy([
                'slug' => $slug,
            ]);

        $children = $category->getChildren()->toArray();
        $products = $marketProductRepository->findProductsByParentCategory($slug, $this->offset, $this->limit);
        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'children' => $children,
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
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
        $child = $request->get('child');
        $category = $em->getRepository(MarketCategory::class)->findOneBy(['slug' => $child]);

        $products = $marketProductRepository->findProductsByChildCategory($category->getId(), $this->offset, $this->limit);

        $customer = $em->getRepository(MarketCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        return $this->render('market_place/category/index.html.twig', [
            'category' => $category,
            'products' => $products['data'],
            'rows_count' => $products['rows_count'],
            'customer' => $customer,
        ]);
    }
}
