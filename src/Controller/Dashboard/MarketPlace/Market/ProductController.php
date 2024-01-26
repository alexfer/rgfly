<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Helper\MarketPlace\MarketAttributeValues;
use App\Entity\MarketPlace\{MarketBrand,
    MarketCategory,
    MarketCategoryProduct,
    MarketManufacturer,
    MarketProduct,
    MarketProductAttribute,
    MarketProductAttributeValue,
    MarketProductBrand,
    MarketProductManufacturer,
    MarketProductSupplier,
    MarketSupplier
};
use App\Form\Type\Dashboard\MarketPlace\ProductType;
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Security\Voter\ProductVoter;
use App\Service\Dashboard;
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\MarketTrait;
use DateTime;
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
        $repository = $em->getRepository(MarketCategoryProduct::class);

        if ($form->isSubmitted() && $form->isValid()) {

            // TODO: rewrite in future
            $repository->removeCategoryProduct($product);
            $entryCategory = new MarketCategoryProduct();
            $entryCategory->setProduct($product)
                ->setCategory($categoryRepository->find($form->get('category')->getData()));
            $em->persist($entryCategory);

            $em = $this->handleRelations($em, $form, $product);
            $attributes['colors'] = $form->get('color')->getData();
            $attributes['size'] = $form->get('size')->getData();

            foreach ($product->getMarketProductAttributes() as $attribute) {
                $values = $attribute->getMarketProductAttributeValues();
                foreach ($values as $value) {
                    $em->remove($value);
                    $em->flush();
                }
            }

            if ($attributes['colors']) {
                $attributeColors = array_flip(MarketAttributeValues::ATTRIBUTES['Color']);

                $attribute = $em->getRepository(MarketProductAttribute::class)->findOneBy(['product' => $product, 'name' => 'color']);

                if (!$attribute) {
                    $attribute = new MarketProductAttribute();
                    $attribute->setProduct($product)->setName('color')->setInFront(1);
                    $em->persist($attribute);
                }

                foreach ($attributes['colors'] as $color) {
                    $attributeValue = new MarketProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($attributeColors[$color])->setExtra([$color]);
                    $em->persist($value);
                }
            }

            if ($attributes['size']) {
                $attributeSize = array_flip(MarketAttributeValues::ATTRIBUTES['Size']);

                $attribute = $em->getRepository(MarketProductAttribute::class)->findOneBy(['product' => $product, 'name' => 'size']);

                if (!$attribute) {
                    $attribute = new MarketProductAttribute();
                    $attribute->setProduct($product)->setName('size')->setInFront(1);
                    $em->persist($attribute);
                }

                foreach ($attributes['size'] as $size) {
                    $attributeValue = new MarketProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($attributeSize[$size]);
                    $em->persist($value);
                }
            }

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

        if ($form->isSubmitted() && $form->isValid()) {

            $requestCategory = $form->get('category')->getData();

            if ($requestCategory) {
                $productCategory = new MarketCategoryProduct();
                $productCategory->setProduct($product)
                    ->setCategory($em->getRepository(MarketCategory::class)->find($requestCategory));
                $em->persist($productCategory);
            }

            $product->setMarket($market);
            $em->persist($product);

            if ($market->getDeletedAt()) {
                $date = new DateTime('@' . strtotime('now'));
                $product->setDeletedAt($date);
            }

            $em = $this->handleRelations($em, $form, $product);
            $product->setSlug(MarketPlaceHelper::slug($product->getId()));
            $em->persist($product);

            $attributes['colors'] = $form->get('color')->getData();
            $attributes['size'] = $form->get('size')->getData();

            if ($attributes['colors']) {
                $attribute = new MarketProductAttribute();
                $attribute->setProduct($product)->setName('color')->setInFront(1);
                $attributeColors = array_flip(MarketAttributeValues::ATTRIBUTES['Color']);

                foreach ($attributes['colors'] as $color) {
                    $attributeValue = new MarketProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($attributeColors[$color])->setExtra([$color]);
                    $em->persist($value);
                }
                $em->persist($attribute);
            }
            if ($attributes['size']) {
                $attribute = new MarketProductAttribute();
                $attribute->setProduct($product)->setName('size')->setInFront(1);

                foreach ($attributes['size'] as $size) {
                    $attributeValue = new MarketProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($size);
                    $em->persist($value);
                }
                $em->persist($attribute);
            }

            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_product', ['market' => $request->get('market'), 'id' => $product->getId()]);
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', $this->navbar() + [
                'form' => $form,
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
