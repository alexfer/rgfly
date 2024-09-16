<?php declare(strict_types=1);

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\Enum\EnumOperation;
use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreOperation;
use App\Entity\MarketPlace\StoreProduct;
use App\Service\MarketPlace\Dashboard\Operation\Interface\OperationInterface;
use App\Service\MarketPlace\Dashboard\Store\Interface\ServeStoreInterface as StoreInterface;
use App\Service\MarketPlace\StoreTrait;
use App\Service\Validator\Interface\OperationFileValidatorInterface;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/market-place/operation')]
class OperationController extends AbstractController
{
    use StoreTrait;

    /**
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws Exception
     */
    #[Route('', name: 'app_dashboard_market_place_operation')]
    public function index(
        EntityManagerInterface $manager,
    ): Response
    {
        $stores = $manager->getRepository(Store::class)->stores($this->getUser());

        return $this->render('dashboard/content/market_place/operation/index.html.twig', [
            'stores' => $stores['result'],
        ]);
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $params
     * @param StoreInterface $serve
     * @return Response
     */
    #[Route('/{store}/import', name: 'app_dashboard_market_place_operation_import', methods: ['GET', 'POST'])]
    public function import(
        Request               $request,
        ParameterBagInterface $params,
        OperationInterface    $operation,
        StoreInterface        $serve,
    ): Response
    {
        $store = $this->store($serve, $this->getUser());

        return $this->render('dashboard/content/market_place/operation/import.html.twig', [
            'store' => $store,
            'formats' => array_map(fn($case) => mb_strtoupper($case->value), EnumOperation::cases()),
            'maxSize' => ini_get('post_max_size'),
            'items' => $operation->fetch($store, true),
        ]);
    }

    /**
     * @param Request $request
     * @param OperationInterface $operation
     * @param StoreInterface $serve
     * @return Response
     */
    #[Route('/{store}/export', name: 'app_dashboard_market_place_operation_export', methods: ['GET', 'POST'])]
    public function export(
        Request            $request,
        OperationInterface $operation,
        StoreInterface     $serve,
    ): Response
    {
        $store = $this->store($serve, $this->getUser());

        if ($request->isMethod('POST')) {

            $options = $request->request->all();
            $operation = $operation->export(StoreProduct::class, $options, $store);

            if ($operation) {
                return $this->redirectToRoute('app_dashboard_market_place_operation_export', [
                    'store' => $store->getId(),
                ]);
            }
        }

        return $this->render('dashboard/content/market_place/operation/export.html.twig', [
            'items' => $operation->fetch($store),
            'store' => $store,
            'formats' => EnumOperation::cases(),
            'options' => $operation->metadata(),
        ]);
    }

    /**
     * @param Request $request
     * @param OperationInterface $operation
     * @param Filesystem $filesystem
     * @param StoreInterface $serveStore
     * @return Response
     */
    #[Route('/{store}/download/{revision}.{format}', name: 'app_dashboard_market_place_operation_download')]
    public function download(
        Request            $request,
        OperationInterface $operation,
        Filesystem         $filesystem,
        StoreInterface     $serveStore,
    ): Response
    {
        $store = $this->store($serveStore, $this->getUser());

        $revision = $request->get('revision');
        $format = $request->get('format');
        $storage = $operation->storage($format);

        $file = sprintf('%s/%s.%s', $storage, $revision, $format);

        if (!$filesystem->exists($file)) {
            $this->addFlash('danger', 'File not found');
            return $this->redirectToRoute('app_dashboard_market_place_operation_export', [
                'store' => $store->getId(),
            ]);
        }

        return $this->file($file, sprintf("products-%d.%s", $revision, $format));
    }

    /**
     * @param Request $request
     * @param ParameterBagInterface $params
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @param StoreInterface $serveStore
     * @return Response
     */
    #[Route('/{store}/upload', name: 'app_dashboard_market_place_operation_upload', methods: ['GET', 'POST'])]
    public function upload(
        Request                         $request,
        ParameterBagInterface           $params,
        SluggerInterface                $slugger,
        EntityManagerInterface          $manager,
        TranslatorInterface             $translator,
        StoreInterface                  $serveStore,
        OperationFileValidatorInterface $fileValidator
    ): Response
    {
        $store = $this->store($serveStore, $this->getUser());
        $storageTmp = $params->get('kernel.project_dir') . '/var/tmp';
        $file = $request->files->get('file');
        $fileName = $operation = null;

        if ($request->isMethod('POST')) {

            $constraint = $fileValidator->validate($file, $translator);

            if ($constraint->count() > 0) {
                return $this->json(['error' => $constraint->get(0)->getMessage()], Response::HTTP_BAD_REQUEST);
            }

            $format = $file->guessExtension();
            //$format = str_replace('txt', 'xls', $format);

            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFileName = $slugger->slug($originalFileName);
            $fileName = $safeFileName . '-' . uniqid() . '.' . $format;

            try {
                $file->move($storageTmp, $fileName);
            } catch (IOExceptionInterface $exception) {
                return $this->json(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $operation = new StoreOperation();
            $operation->setStore($store);
            $operation->setRevision((string)time());
            $operation->setFormat(EnumOperation::from($format));
            $operation->setFilename($fileName);
            $manager->persist($operation);
            $manager->flush();
        }

        return $this->json([
            'success' => $translator->trans('text.file.uploaded'),
            'operation' => [
                'url' => $this->generateUrl('app_dashboard_market_place_operation_remove', [
                    'store' => $store->getId(),
                    'id' => $operation->getId(),
                ]),
                'store' => $operation->getStore()->getName(),
                'format' => $operation->getFormat()->name,
                'filename' => $fileName,
                'createdAt' => $operation->getCreatedAt()->format('D, d M Y H:i'),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param OperationInterface $operation
     * @param StoreInterface $serveStore
     * @return Response
     */
    #[Route('/{store}/rm/{id}', name: 'app_dashboard_market_place_operation_remove', methods: ['POST'])]
    public function remove(
        Request               $request,
        OperationInterface    $operation,
        StoreInterface        $serveStore,
        ParameterBagInterface $params,
    ): Response
    {
        $store = $this->store($serveStore, $this->getUser());

        if ($item = $operation->find($store, (int)$request->get('id'))) {
            $storage = $params->get('kernel.project_dir') . '/var/tmp';
            $file = sprintf('%s/%s', $storage, $item->getFilename());
            $operation->prune($file, $item);
        }

        return $this->json(['success' => true], Response::HTTP_OK);
    }
}