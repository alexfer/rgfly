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
use App\Helper\MarketPlace\{MarketAttributeValues};
use App\Security\Voter\ProductVoter;
use App\Service\FileUploader;
use App\Service\Interface\ImageValidatorInterface;
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\Dashboard\Category\Interface\ServeInterface as StoreCategoryInterface;
use App\Service\MarketPlace\Dashboard\Product\Interface\ServeInterface as ProductServiceInterface;
use App\Service\MarketPlace\Dashboard\Store\Interface\ServeInterface as StoreInterface;
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
     * @param ProductServiceInterface $serve
     * @param StoreInterface $iStore
     * @return Response
     */
    #[Route('/{store}/{search}', name: 'app_dashboard_market_place_market_product', defaults: ['search' => null])]
    public function index(
        Request                 $request,
        UserInterface           $user,
        ProductServiceInterface $serve,
        StoreInterface          $iStore,
    ): Response
    {
        $store = $this->store($iStore, $user);
        $serve->handle($user);

        return $this->render('dashboard/content/market_place/product/index.html.twig', [
            'store' => $store,
            'currency' => Currency::currency($serve->currency($store)),
            'products' => $serve->index($store, $request->query->get('search')),
            'coupons' => $serve->coupon($store, StoreCoupon::COUPON_PRODUCT),
        ]);
    }

    /**
     * @param Request $request
     * @param StoreProduct $product
     * @param UserInterface $user
     * @param TranslatorInterface $translator
     * @param ProductServiceInterface $serve
     * @param StoreInterface $iStore
     * @return Response
     */
    #[Route('/edit/{store}/{id}/{tab}', name: 'app_dashboard_market_place_edit_product', methods: ['GET', 'POST'])]
    #[IsGranted(ProductVoter::EDIT, subject: 'product', statusCode: Response::HTTP_FORBIDDEN)]
    public function edit(
        Request                 $request,
        StoreProduct            $product,
        UserInterface           $user,
        TranslatorInterface     $translator,
        ProductServiceInterface $serve,
        StoreInterface          $iStore,
    ): Response
    {
        $store = $this->store($iStore, $user);
        $form = $this->createForm(ProductType::class, $product);
        $handle = $serve->handle($user, $form);

        if ($handle->isSubmitted() && $handle->isValid()) {
            $product = $serve->update($store, $product);

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
     * @param TranslatorInterface $translator
     * @param ProductServiceInterface $serve
     * @param StoreInterface $iStore
     * @return Response
     */
    #[Route('/create/{store}/{tab}', name: 'app_dashboard_market_place_create_product', methods: ['GET', 'POST'])]
    public function create(
        Request                 $request,
        UserInterface           $user,
        TranslatorInterface     $translator,
        ProductServiceInterface $serve,
        StoreInterface          $iStore,
    ): Response
    {
        $store = $this->store($iStore, $user);
        $product = new StoreProduct();

        $form = $this->createForm(ProductType::class, $product);
        $handle = $serve->handle($user, $form);

        if ($handle->isSubmitted() && $handle->isValid()) {
            $product = $serve->create($store, $product);
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
