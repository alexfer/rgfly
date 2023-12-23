<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketCategoryProduct;
use App\Entity\MarketPlace\MarketProduct;
use App\Form\Type\Dashboard\MarketPlace\ProductType;
use App\Repository\MarketPlace\MarketCategoryProductRepository;
use App\Repository\MarketPlace\MarketCategoryRepository;
use App\Repository\MarketPlace\MarketProductRepository;
use App\Repository\MarketPlace\MarketRepository;
use App\Service\Navbar;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/market-place/product')]
class ProductController extends AbstractController
{
    use Navbar;

    #[Route('/', name: 'app_dashboard_market_place_product')]
    public function index(
        UserInterface           $user,
        MarketRepository        $marketRepository,
        MarketProductRepository $marketProductRepository,
    ): Response
    {
        $market = $marketRepository->findOneBy(['owner' => $user], ['id' => 'desc']);
        $entries = $marketProductRepository->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/product/index.html.twig', $this->build($user) + [
                'market' => $market,
                'entries' => $entries,
            ]);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_market_place_edit_product', methods: ['GET', 'POST'])]
    public function edit(
        Request                  $request,
        UserInterface            $user,
        MarketProduct            $entry,
        MarketCategoryRepository $categoryRepository,
        MarketCategoryProductRepository  $marketCategoryProductRepository,
        EntityManagerInterface   $em,
        TranslatorInterface      $translator,
        SluggerInterface         $slugger,
    ): Response
    {
        $form = $this->createForm(ProductType::class, $entry);
        $form->handleRequest($request);
        $categories = $categoryRepository->findBy([], ['name' => 'asc']);
        $uniqueError = null;
        $name = $form->get('name')->getData();

        if ($name) {
            try {
                $entry->setSlug($slugger->slug($name)->lower());
                $em->persist($entry);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $uniqueError = $translator->trans('slug.unique', [
                    '%name%' => $translator->trans('label.form.title'),
                    '%value%' => $name,
                ], 'validators');
            }
        }

        if ($form->isSubmitted() && $form->isValid() && !$uniqueError) {
            $requestCategory = $request->get('category');
            if ($requestCategory) {
                $marketCategoryProductRepository->removeCategoryProduct($entry);
                foreach ($requestCategory as $key => $value) {
                    $entryCategory = new MarketCategoryProduct();
                    $entryCategory->setProduct($entry)
                        ->setCategory($categoryRepository->findOneBy(['id' => $key]));
                    $em->persist($entryCategory);
                }
            }
            $em->persist($entry);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));

            return $this->redirectToRoute('app_dashboard_market_place_edit_product', ['id' => $entry->getId()]);
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', $this->build($user) + [
                'form' => $form,
                'error' => $uniqueError,
                'entry' => $entry,
                'categories' => $categories,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param SluggerInterface $slugger
     * @param MarketCategoryRepository $categoryRepository
     * @param MarketRepository $marketRepository
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/create', name: 'app_dashboard_market_place_create_product')]
    public function create(
        Request                  $request,
        UserInterface            $user,
        SluggerInterface         $slugger,
        MarketCategoryRepository $categoryRepository,
        MarketRepository         $marketRepository,
        EntityManagerInterface   $em,
        TranslatorInterface      $translator,
    ): Response
    {
        $categories = $categoryRepository->findBy([], ['name' => 'asc']);

        $entry = new MarketProduct();
        $market = $marketRepository->findOneBy(['owner' => $user]);

        $form = $this->createForm(ProductType::class, $entry);
        $form->handleRequest($request);

        $uniqueError = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->get('name')->getData();
            $slug = $slugger->slug($name)->lower();

            $requestCategory = $request->get('category');

            if ($requestCategory) {
                foreach ($requestCategory as $key => $value) {
                    $entryCategory = new MarketCategoryProduct();
                    $entryCategory->setProduct($entry)
                        ->setCategory($categoryRepository->findOneBy(['id' => $key]));
                    $em->persist($entryCategory);
                }
            }

            try {
                $entry->setSlug($slug);
                $entry->setMarket($market);
                $em->persist($entry);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $uniqueError = $translator->trans('slug.unique', [
                    '%name%' => $translator->trans('label.form.product_name'),
                    '%value%' => $name,
                ], 'validators');
            }

            if (!$uniqueError) {
                $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
                return $this->redirectToRoute('app_dashboard_market_place_edit_product', ['id' => $entry->getId()]);
            }
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', $this->build($user) + [
                'form' => $form,
                'error' => $uniqueError,
                'entry' => $entry,
                'categories' => $categories,
            ]);
    }
}
