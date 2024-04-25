<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\Attach;
use App\Entity\MarketPlace\{MarketBrand,
    MarketCategory,
    MarketCategoryProduct,
    MarketManufacturer,
    MarketProduct,
    MarketProductAttach,
    MarketProductAttribute,
    MarketProductAttributeValue,
    MarketProductBrand,
    MarketProductManufacturer,
    MarketProductSupplier,
    MarketSupplier};
use App\Form\Type\Dashboard\MarketPlace\ProductType;
use App\Helper\MarketPlace\MarketAttributeValues;
use App\Helper\MarketPlace\MarketPlaceHelper;
use App\Repository\MarketPlace\MarketProductAttachRepository;
use App\Repository\MarketPlace\MarketProductRepository;
use App\Security\Voter\ProductVoter;
use App\Service\Dashboard;
use App\Service\FileUploader;
use App\Service\Interface\ImageValidatorInterface;
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\MarketTrait;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
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
     * @return Response
     * @throws Exception
     */
    #[Route('/edit/{market}-{id}/{tab}', name: 'app_dashboard_market_place_edit_product', methods: ['GET', 'POST'])]
    #[IsGranted(ProductVoter::EDIT, subject: 'product', statusCode: Response::HTTP_FORBIDDEN)]
    public function edit(
        Request                $request,
        MarketProduct          $product,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
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

            $sku = $form->get('sku')->getData();
            if (!$sku) {
                $sku = 'M' . $request->get('market') . '-C' . $form->get('category')->getData() . '-P' . $product->getId() . '-N-' . mb_substr($form->get('name')->getData(), 0, 4, 'utf8') . '-C' . (int)$form->get('cost')->getData();
            }
            $product->setSku(strtoupper($sku));

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));

            return $this->redirectToRoute('app_dashboard_market_place_edit_product', [
                'market' => $request->get('market'),
                'id' => $product->getId(),
                'tab' => $request->get('tab'),
            ]);
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', $this->navbar() + [
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
     * @throws Exception
     */
    #[Route('/create/{market}/{tab}', name: 'app_dashboard_market_place_create_product', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $market = $this->market($request, $user, $em);

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

            $em->persist($market);
            $em->flush();

            $em = $this->handleRelations($em, $form, $product);
            $product->setSlug(MarketPlaceHelper::slug($product->getId()));

            $sku = $form->get('sku')->getData();
            if (!$sku) {
                $sku = 'M' . $request->get('market') . '-C' . $requestCategory . '-P' . $product->getId() . '-N-' . mb_substr($form->get('name')->getData(), 0, 4, 'utf8') . '-C' . (int)$form->get('cost')->getData();
            }
            $product->setSku($sku);
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
            return $this->redirectToRoute('app_dashboard_market_place_edit_product', [
                'market' => $request->get('market'),
                'id' => $product->getId(),
                'tab' => $request->get('tab'),
            ]);
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', $this->navbar() + [
                'form' => $form,
                'errors' => $form->getErrors(true),
            ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param FormInterface $form
     * @param MarketProduct $product
     * @return EntityManagerInterface
     */
    private function handleRelations(
        EntityManagerInterface $em,
        FormInterface          $form,
        MarketProduct          $product,
    ): EntityManagerInterface
    {

        $supplier = $em->getRepository(MarketSupplier::class)
            ->findOneBy(['id' => $form->get('supplier')->getData()]);
        $brand = $em->getRepository(MarketBrand::class)
            ->findOneBy(['id' => $form->get('brand')->getData()]);
        $manufacturer = $em->getRepository(MarketManufacturer::class)
            ->findOneBy(['id' => $form->get('manufacturer')->getData()]);

        if ($supplier) {
            $ps = $product->getMarketProductSupplier();
            if (!$ps) {
                $ps = new MarketProductSupplier();
            }
            $ps->setProduct($product)->setSupplier($supplier);
            $em->persist($ps);
        } else {
            $ps = $product->getMarketProductSupplier();
            if ($ps) {
                $ps->setProduct(null)->setSupplier(null);
                $em->persist($ps);
                $em->flush();
                $em->getRepository(MarketProductSupplier::class)->drop($ps->getId());
            }
        }

        if ($brand) {
            $pb = $product->getMarketProductBrand();
            if (!$pb) {
                $pb = new MarketProductBrand();
            }
            $pb->setProduct($product)->setBrand($brand);
            $em->persist($pb);
        } else {
            $pb = $product->getMarketProductBrand();
            if ($pb) {
                $pb->setProduct(null)->setBrand(null);
                $em->persist($pb);
                $em->flush();
                $em->getRepository(MarketProductBrand::class)->drop($pb->getId());
            }
        }

        if ($manufacturer) {
            $pm = $product->getMarketProductManufacturer();
            if (!$pm) {
                $pm = new MarketProductManufacturer();
            }
            $pm->setProduct($product)->setManufacturer($manufacturer);
            $em->persist($pm);
        } else {
            $pm = $product->getMarketProductManufacturer();
            if ($pm) {
                $pm->setProduct(null)->setManufacturer(null);
                $em->persist($pm);
                $em->flush();
                $em->getRepository(MarketProductManufacturer::class)->drop($pm->getId());
            }
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
        $token = $request->get('_token');

        if ($request->headers->get('Content-Type', 'application/json')) {
            $content = $request->getContent();
            $content = json_decode($content, true);
            $token = $content['_token'];
        }

        if ($this->isCsrfTokenValid('delete', $token)) {
            $date = new DateTime('@' . strtotime('now'));
            $product->setDeletedAt($date);
            $em->persist($product);
            $em->flush();
        }

        if ($request->headers->get('Content-Type', 'application/json')) {
            return $this->json(['redirect' => $this->generateUrl('app_dashboard_market_place_market_product', ['market' => $market->getId()])]);
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

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param MarketProductRepository $repository
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @param CacheManager $cacheManager
     * @param ParameterBagInterface $params
     * @param MarketProductAttachRepository $productAttachmentRepository
     * @param ImageValidatorInterface $imageValidator
     * @return Response
     */
    #[Route('/attach/{market}-{id}', name: 'app_dashboard_product_attach')]
    public function attach(
        Request                       $request,
        TranslatorInterface           $translator,
        MarketProductRepository       $repository,
        EntityManagerInterface        $em,
        SluggerInterface              $slugger,
        CacheManager                  $cacheManager,
        ParameterBagInterface         $params,
        MarketProductAttachRepository $productAttachmentRepository,
        ImageValidatorInterface       $imageValidator,
    ): Response
    {
        $file = $request->files->get('file');
        $id = $request->get('id');
        $market = $request->get('market');
        $product = $repository->findOneBy(['id' => $id]);

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
            } catch (Exception $ex) {
                return $this->json([
                    'success' => false,
                    'message' => $ex->getMessage(),
                    'picture' => null,
                ]);
            }

            $productAttachment = new MarketProductAttach();
            $productAttachment->setProduct($product)
                ->setAttach($attach);

            $em->persist($productAttachment);
            $em->flush();
        }

        $storage = $params->get('product_storage_picture');

        $url = "{$storage}/{$product->getId()}/{$attach->getName()}";
        $picture = $cacheManager->getBrowserPath(parse_url($url, PHP_URL_PATH), 'product_preview');

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
    #[Route('/attach/remove/{market}-{id}', name: 'app_dashboard_product_attach_remove', methods: ['POST'])]
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
        $product = $em->getRepository(MarketProduct::class)->find($request->get('id'));
        $productAttach = $em->getRepository(MarketProductAttach::class)->findOneBy(['attach' => $attach, 'product' => $product]);

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
        $storage = sprintf('%s/picture/', $params->get('product_storage_dir'));
        return $storage . $id;
    }
}
