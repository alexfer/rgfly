<?php

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\Attach;
use App\Entity\MarketPlace\{StoreBrand,
    StoreCategory,
    StoreCategoryProduct,
    StoreCoupon,
    StoreManufacturer,
    StoreProduct,
    StoreProductAttach,
    StoreProductAttribute,
    StoreProductAttributeValue,
    StoreProductBrand,
    StoreProductManufacturer,
    StoreProductSupplier,
    StoreSupplier};
use App\Form\Type\Dashboard\MarketPlace\ProductType;
use App\Helper\MarketPlace\{MarketAttributeValues, MarketPlaceHelper};
use App\Security\Voter\ProductVoter;
use App\Service\FileUploader;
use App\Service\Interface\ImageValidatorInterface;
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\StoreTrait;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/market-place/product')]
class ProductController extends AbstractController
{
    use StoreTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{store}/{search}', name: 'app_dashboard_market_place_market_product', defaults: ['search' => null])]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $currency = Currency::currency($store->getCurrency());
        $products = $em->getRepository(StoreProduct::class)->products($store, $request->query->get('search'));
        $coupons = $em->getRepository(StoreCoupon::class)->fetchActive($store);

        return $this->render('dashboard/content/market_place/product/index.html.twig', [
            'store' => $store,
            'currency' => $currency,
            'products' => $products,
            'coupons' => $coupons,
        ]);
    }

    /**
     * @param Request $request
     * @param StoreProduct $product
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Exception
     */
    #[Route('/edit/{store}/{id}/{tab}', name: 'app_dashboard_market_place_edit_product', methods: ['GET', 'POST'])]
    #[IsGranted(ProductVoter::EDIT, subject: 'product', statusCode: Response::HTTP_FORBIDDEN)]
    public function edit(
        Request                $request,
        StoreProduct           $product,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $categoryRepository = $em->getRepository(StoreCategory::class);
        $repository = $em->getRepository(StoreCategoryProduct::class);

        if ($form->isSubmitted() && $form->isValid()) {

            // TODO: rewrite in future
            $repository->removeCategoryProduct($product);
            $entryCategory = new StoreCategoryProduct();
            $entryCategory->setProduct($product)
                ->setCategory($categoryRepository->find($form->get('category')->getData()));
            $em->persist($entryCategory);

            $em = $this->handleRelations($em, $form, $product);
            $attributes['colors'] = $form->get('color')->getData();
            $attributes['size'] = $form->get('size')->getData();

            foreach ($product->getStoreProductAttributes() as $attribute) {
                $values = $attribute->getStoreProductAttributeValues();
                foreach ($values as $value) {
                    $em->remove($value);
                    $em->flush();
                }
            }

            if ($attributes['colors']) {
                $attributeColors = array_flip(MarketAttributeValues::ATTRIBUTES['Color']);

                $attribute = $em->getRepository(StoreProductAttribute::class)->findOneBy(['product' => $product, 'name' => 'color']);

                if (!$attribute) {
                    $attribute = new StoreProductAttribute();
                    $attribute->setProduct($product)->setName('color')->setInFront(1);
                    $em->persist($attribute);
                }

                foreach ($attributes['colors'] as $color) {
                    $attributeValue = new StoreProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($attributeColors[$color])->setExtra([$color]);
                    $em->persist($value);
                }
            }

            if ($attributes['size']) {
                $attributeSize = array_flip(MarketAttributeValues::ATTRIBUTES['Size']);

                $attribute = $em->getRepository(StoreProductAttribute::class)->findOneBy(['product' => $product, 'name' => 'size']);

                if (!$attribute) {
                    $attribute = new StoreProductAttribute();
                    $attribute->setProduct($product)->setName('size')->setInFront(1);
                    $em->persist($attribute);
                }

                foreach ($attributes['size'] as $size) {
                    $attributeValue = new StoreProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($attributeSize[$size]);
                    $em->persist($value);
                }
            }

            $sku = $form->get('sku')->getData();
            if (!$sku) {
                $sku = 'M' . $request->get('store') . '-C' . $form->get('category')->getData() . '-P' . $product->getId() . '-N-' . mb_substr($form->get('name')->getData(), 0, 4, 'utf8') . '-C' . (int)$form->get('cost')->getData();
            }
            $product->setSku(strtoupper($sku));

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));

            return $this->redirectToRoute('app_dashboard_market_place_edit_product', [
                'store' => $request->get('store'),
                'id' => $product->getId(),
                'tab' => $request->get('tab'),
            ]);
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', [
            'form' => $form,
            'errors' => $form->getErrors(true),
            'product' => $product,
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    #[Route('/create/{store}/{tab}', name: 'app_dashboard_market_place_create_product', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $store = $this->store($request, $user, $em);

        $product = new StoreProduct();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $requestCategory = $form->get('category')->getData();

            if ($requestCategory) {
                $productCategory = new StoreCategoryProduct();
                $productCategory->setProduct($product)
                    ->setCategory($em->getRepository(StoreCategory::class)->find($requestCategory));
                $em->persist($productCategory);
            }

            $product->setStore($store);
            $em->persist($product);

            if ($store->getDeletedAt()) {
                $date = new \DateTime('@' . strtotime('now'));
                $product->setDeletedAt($date);
            }

            $em->persist($store);
            $em->flush();

            $em = $this->handleRelations($em, $form, $product);
            $product->setSlug(MarketPlaceHelper::slug($product->getId()));

            $sku = $form->get('sku')->getData();
            if (!$sku) {
                $sku = 'M' . $request->get('store') . '-C' . $requestCategory . '-P' . $product->getId() . '-N-' . mb_substr($form->get('name')->getData(), 0, 4, 'utf8') . '-C' . (int)$form->get('cost')->getData();
            }
            $product->setSku($sku);
            $em->persist($product);

            $attributes['colors'] = $form->get('color')->getData();
            $attributes['size'] = $form->get('size')->getData();

            if ($attributes['colors']) {
                $attribute = new StoreProductAttribute();
                $attribute->setProduct($product)->setName('color')->setInFront(1);
                $attributeColors = array_flip(MarketAttributeValues::ATTRIBUTES['Color']);

                foreach ($attributes['colors'] as $color) {
                    $attributeValue = new StoreProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($attributeColors[$color])->setExtra([$color]);
                    $em->persist($value);
                }
                $em->persist($attribute);
            }
            if ($attributes['size']) {
                $attribute = new StoreProductAttribute();
                $attribute->setProduct($product)->setName('size')->setInFront(1);

                foreach ($attributes['size'] as $size) {
                    $attributeValue = new StoreProductAttributeValue();
                    $value = $attributeValue->setAttribute($attribute)->setValue($size);
                    $em->persist($value);
                }
                $em->persist($attribute);
            }

            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
            return $this->redirectToRoute('app_dashboard_market_place_edit_product', [
                'store' => $request->get('store'),
                'id' => $product->getId(),
                'tab' => $request->get('tab'),
            ]);
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', [
            'form' => $form,
            'errors' => $form->getErrors(true),
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param FormInterface $form
     * @param StoreProduct $product
     * @return EntityManagerInterface
     */
    private function handleRelations(
        EntityManagerInterface $em,
        FormInterface          $form,
        StoreProduct           $product,
    ): EntityManagerInterface
    {

        $supplier = $em->getRepository(StoreSupplier::class)
            ->findOneBy(['id' => $form->get('supplier')->getData()]);
        $brand = $em->getRepository(StoreBrand::class)
            ->findOneBy(['id' => $form->get('brand')->getData()]);
        $manufacturer = $em->getRepository(StoreManufacturer::class)
            ->findOneBy(['id' => $form->get('manufacturer')->getData()]);

        if ($supplier) {
            $ps = $product->getStoreProductSupplier();
            if (!$ps) {
                $ps = new StoreProductSupplier();
            }
            $ps->setProduct($product)->setSupplier($supplier);
            $em->persist($ps);
        } else {
            $ps = $product->getStoreProductSupplier();
            if ($ps) {
                $ps->setProduct(null)->setSupplier(null);
                $em->persist($ps);
                $em->flush();
                $em->getRepository(StoreProductSupplier::class)->drop($ps->getId());
            }
        }

        if ($brand) {
            $pb = $product->getStoreProductBrand();
            if (!$pb) {
                $pb = new StoreProductBrand();
            }
            $pb->setProduct($product)->setBrand($brand);
            $em->persist($pb);
        } else {
            $pb = $product->getStoreProductBrand();
            if ($pb) {
                $pb->setProduct(null)->setBrand(null);
                $em->persist($pb);
                $em->flush();
                $em->getRepository(StoreProductBrand::class)->drop($pb->getId());
            }
        }

        if ($manufacturer) {
            $pm = $product->getStoreProductManufacturer();
            if (!$pm) {
                $pm = new StoreProductManufacturer();
            }
            $pm->setProduct($product)->setManufacturer($manufacturer);
            $em->persist($pm);
        } else {
            $pm = $product->getStoreProductManufacturer();
            if ($pm) {
                $pm->setProduct(null)->setManufacturer(null);
                $em->persist($pm);
                $em->flush();
                $em->getRepository(StoreProductManufacturer::class)->drop($pm->getId());
            }
        }

        return $em;
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param StoreProduct $product
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    #[Route('/delete/{store}/{id}', name: 'app_dashboard_delete_product', methods: ['POST'])]
    public function delete(
        Request                $request,
        UserInterface          $user,
        StoreProduct           $product,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $token = $request->get('_token');

        if (!$token) {
            $content = $request->getPayload()->all();
            $token = $content['_token'];
        }

        if ($this->isCsrfTokenValid('delete', $token)) {
            $date = new \DateTime('@' . strtotime('now'));
            $product->setDeletedAt($date);
            $em->persist($product);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_market_product', ['store' => $store->getId()]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param StoreProduct $product
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/restore/{store}/{id}', name: 'app_dashboard_restore_product')]
    public function restore(
        Request                $request,
        UserInterface          $user,
        StoreProduct           $product,
        EntityManagerInterface $em,
    ): Response
    {
        $store = $this->store($request, $user, $em);
        $product->setDeletedAt(null);
        $em->persist($product);
        $em->flush();

        return $this->redirectToRoute('app_dashboard_market_place_market_product', ['store' => $store->getId()]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @param CacheManager $cacheManager
     * @param ParameterBagInterface $params
     * @param ImageValidatorInterface $imageValidator
     * @return Response
     */
    #[Route('/attach/{store}/{id}', name: 'app_dashboard_product_attach')]
    public function attach(
        Request                 $request,
        TranslatorInterface     $translator,
        EntityManagerInterface  $em,
        SluggerInterface        $slugger,
        CacheManager            $cacheManager,
        ParameterBagInterface   $params,
        ImageValidatorInterface $imageValidator,
    ): Response
    {
        $file = $request->files->get('file');
        $id = $request->get('id');
        $store = $request->get('store');
        $product = $em->getRepository(StoreProduct::class)->findOneBy(['id' => $id]);

        $attach = null;

        if ($file) {
            $validate = $imageValidator->validate($file, $translator);

            if ($validate->has(0)) {
                return $this->json([
                    'message' => $validate->get(0)->getMessage(),
                    'picture' => null,
                ]);
            }

            $fileUploader = new FileUploader($this->getTargetDir($product->getId(), $params), $slugger, $em);

            try {
                $attach = $fileUploader->upload($file)->handle();
            } catch (\Exception $ex) {
                return $this->json([
                    'success' => false,
                    'message' => $ex->getMessage(),
                    'picture' => null,
                ]);
            }

            $productAttachment = new StoreProductAttach();
            $productAttachment->setProduct($product)
                ->setAttach($attach);

            $em->persist($productAttachment);
            $em->flush();
        }

        $url = $this->getTargetDir($product->getId(), $params);
        $picture = $cacheManager->getBrowserPath(parse_url($url . '/' . $attach->getName(), PHP_URL_PATH), 'product_preview', [], null);

        return $this->json([
            'success' => true,
            'message' => $translator->trans('entry.picture.default'),
            'picture' => $picture,
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param CacheManager $cacheManager
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface $params
     * @return Response
     */
    #[Route('/attach/remove/{store}/{id}', name: 'app_dashboard_product_attach_remove', methods: ['POST'])]
    public function remove(
        Request                $request,
        TranslatorInterface    $translator,
        CacheManager           $cacheManager,
        EntityManagerInterface $em,
        ParameterBagInterface  $params
    ): Response
    {
        $id = $request->getPayload()->get('id');
        $attach = $em->getRepository(Attach::class)->find($id);
        $product = $em->getRepository(StoreProduct::class)->find($request->get('id'));
        $productAttach = $em->getRepository(StoreProductAttach::class)->findOneBy(['attach' => $attach, 'product' => $product]);

        $fs = new Filesystem();
        $oldFile = $this->getTargetDir($product->getId(), $params) . '/' . $attach->getName();

        // TODO: fix it
        if ($cacheManager->isStored($oldFile, 'product_preview')) {
            $cacheManager->remove($oldFile, 'product_preview');
        }

        if ($cacheManager->isStored($oldFile, 'product_view')) {
            $cacheManager->remove($oldFile, 'product_view');
        }

        if ($fs->exists($oldFile)) {
            $fs->remove($oldFile);
        }

        $productAttach->setAttach(null)->setProduct(null);

        $em->remove($productAttach);
        $em->remove($attach);
        $em->flush();

        return $this->json(['message' => $translator->trans('user.picture.delete'), 'file' => $attach->getName()]);
    }

    /**
     * @param int|null $id
     * @param ParameterBagInterface $params
     * @return string
     */
    private function getTargetDir(?int $id, ParameterBagInterface $params): string
    {
        return sprintf('%s/%d', $params->get('product_storage_dir'), $id);
    }
}
