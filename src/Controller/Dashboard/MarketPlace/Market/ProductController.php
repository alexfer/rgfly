<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketCategoryProduct;
use App\Entity\MarketPlace\MarketManufacturer;
use App\Entity\MarketPlace\MarketProduct;
use App\Entity\MarketPlace\MarketProductManufacturer;
use App\Entity\MarketPlace\MarketProductBrand;
use App\Entity\MarketPlace\MarketProductSupplier;
use App\Entity\MarketPlace\MarketBrand;
use App\Entity\MarketPlace\MarketSupplier;
use App\Form\Type\Dashboard\MarketPlace\ProductType;
use App\Security\Voter\ProductVoter;
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\MarketTrait;
use App\Service\Dashboard;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/market-place/product')]
class ProductController extends AbstractController
{
    use Dashboard, MarketTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{market}', name: 'app_dashboard_market_place_market_product')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $currency = Currency::currency($market->getCurrency());
        $products = $em->getRepository(MarketProduct::class)->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/product/index.html.twig', $this->navbar() + [
                'market' => $market,
                'currency' => $currency,
                'products' => $products,
            ]);
    }

    /**
     * @param Request $request
     * @param MarketProduct $product
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param SluggerInterface $slugger
     * @return Response
     * @throws Exception
     */
    #[Route('/edit/{market}-{id}', name: 'app_dashboard_market_place_edit_product', methods: ['GET', 'POST'])]
    #[IsGranted(ProductVoter::EDIT, subject: 'product', statusCode: Response::HTTP_FORBIDDEN)]
    public function edit(
        Request                $request,
        MarketProduct          $product,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
        SluggerInterface       $slugger,
    ): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $categoryRepository = $em->getRepository(MarketCategory::class);
        $categories = $categoryRepository->findBy([], ['name' => 'asc']);
        $repository = $em->getRepository(MarketCategoryProduct::class);

        $uniqueError = null;
        $name = $form->get('name')->getData();

        if ($name) {
            try {
                $product->setSlug($slugger->slug($name)->lower());
                $em->persist($product);
            } catch (UniqueConstraintViolationException $e) {
                $uniqueError = $translator->trans('slug.unique', [
                    '%name%' => $translator->trans('label.form.title'),
                    '%value%' => $name,
                ], 'validators');
            }
        }

        if ($form->isSubmitted() && $form->isValid() && !$uniqueError) {
            $requestCategory = $form->get('category')->getData();
            $repository->removeCategoryProduct($product);

            if (count($requestCategory)) {
                foreach ($requestCategory as $value) {
                    $entryCategory = new MarketCategoryProduct();
                    $entryCategory->setProduct($product)
                        ->setCategory($categoryRepository->findOneBy(['id' => $value]));
                    $em->persist($entryCategory);
                }
            } else {
                $repository->removeCategoryProduct($product);
            }

            $em = $this->handleRelations($em, $form, $product);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));

            return $this->redirectToRoute('app_dashboard_market_place_edit_product', [
                'market' => $request->get('market'),
                'id' => $product->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', $this->navbar() + [
                'form' => $form,
                'error' => $uniqueError,
                'categories' => $categories,
                'productCategory' => $repository->findBy(['product' => $product]),
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route('/create/{market}', name: 'app_dashboard_market_place_create_product', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        SluggerInterface       $slugger,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $categories = $em->getRepository(MarketCategory::class)->findBy([], ['name' => 'asc']);

        $product = new MarketProduct();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $uniqueError = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->get('name')->getData();
            $slug = $slugger->slug($name)->lower();

            $requestCategory = $form->get('category')->getData();

            if ($requestCategory) {
                foreach ($requestCategory as $value) {
                    $productCategory = new MarketCategoryProduct();
                    $productCategory->setProduct($product)
                        ->setCategory($em->getRepository(MarketCategory::class)->findOneBy(['id' => $value]));
                    $em->persist($productCategory);
                }
            }

            try {
                $product->setSlug($slug)
                    ->setMarket($market);
                $em->persist($product);
            } catch (UniqueConstraintViolationException $e) {
                $uniqueError = $translator->trans('slug.unique', [
                    '%name%' => $translator->trans('label.form.product_name'),
                    '%value%' => $name,
                ], 'validators');
            }

            if($market->getDeletedAt()) {
                $date = new DateTime('@' . strtotime('now'));
                $product->setDeletedAt($date);
            }

            $em = $this->handleRelations($em, $form, $product);

            $em->flush();

            if (!$uniqueError) {
                $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
                return $this->redirectToRoute('app_dashboard_market_place_edit_product', ['market' => $request->get('market'), 'id' => $product->getId()]);
            }
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', $this->navbar() + [
                'form' => $form,
                'error' => $uniqueError,
                'categories' => $categories,
            ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param FormInterface $form
     * @param MarketProduct $entry
     * @return EntityManagerInterface
     */
    private function handleRelations(
        EntityManagerInterface $em,
        FormInterface          $form,
        MarketProduct          $entry,
    ): EntityManagerInterface
    {
        $supplier = $em->getRepository(MarketSupplier::class)
            ->findOneBy(['id' => $form->get('supplier')->getData()]);
        $brand = $em->getRepository(MarketBrand::class)
            ->findOneBy(['id' => $form->get('brand')->getData()]);
        $manufacturer = $em->getRepository(MarketManufacturer::class)
            ->findOneBy(['id' => $form->get('manufacturer')->getData()]);

        if ($supplier) {
            $ps = $entry->getMarketProductSupplier();
            if (!$ps) {
                $ps = new MarketProductSupplier();
            }
            $ps->setProduct($entry)->setSupplier($supplier);
            $em->persist($ps);
        }

        if ($brand) {
            $pp = $entry->getMarketProductBrand();
            if (!$pp) {
                $pp = new MarketProductBrand();
            }
            $pp->setProduct($entry)->setBrand($brand);
            $em->persist($pp);
        }
        if ($manufacturer) {
            $pm = $entry->getMarketProductManufacturer();
            if (!$pm) {
                $pm = new MarketProductManufacturer();
            }
            $pm->setProduct($entry)->setManufacturer($manufacturer);
            $em->persist($pm);
        }

        return $em;
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketProduct $product
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route('/delete/{market}-{id}', name: 'app_dashboard_delete_product', methods: ['POST'])]
    public function delete(
        Request                $request,
        UserInterface          $user,
        MarketProduct          $product,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);

        if ($this->isCsrfTokenValid('delete', $request->get('_token'))) {
            $date = new DateTime('@' . strtotime('now'));
            $product->setDeletedAt($date);
            $em->persist($product);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_market_product', ['market' => $market->getId()]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketProduct $product
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/restore/{market}-{id}', name: 'app_dashboard_restore_product')]
    public function restore(
        Request                $request,
        UserInterface          $user,
        MarketProduct          $product,
        EntityManagerInterface $em,
    ): Response
    {
        $market = $this->market($request, $user, $em);
        $product->setDeletedAt(null);
        $em->persist($product);
        $em->flush();

        return $this->redirectToRoute('app_dashboard_market_place_market_product', ['market' => $market->getId()]);
    }
}
