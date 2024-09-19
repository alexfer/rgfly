<?php declare(strict_types=1);

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\StoreCarrier;
use App\Entity\MarketPlace\StorePaymentGateway;
use App\Entity\MarketPlace\StorePaymentGatewayStore;
use App\Form\Type\Dashboard\MarketPlace\CarrierType;
use App\Form\Type\Dashboard\MarketPlace\PaymentGatewayType;
use App\Service\FileUploader;
use App\Service\Validator\Interface\CarrierValidatorInterface;
use App\Service\Validator\Interface\PaymentGatewayValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/config/setup')]
#[IsGranted('ROLE_ADMIN', message: 'Access denied.')]
class ConfigurationController extends AbstractController
{
    /**
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/{tab?}', name: 'app_dashboard_config', methods: ['GET'])]
    public function index(
        EntityManagerInterface $manager,
    ): Response
    {
        $pgForm = $this->createForm(PaymentGatewayType::class, new StorePaymentGateway());
        $carrierForm = $this->createForm(CarrierType::class, new StoreCarrier());

        $carriers = $manager->getRepository(StoreCarrier::class)->findBy([], ['id' => 'DESC']);
        $paymentGateways = $manager->getRepository(StorePaymentGateway::class)->findBy([], ['id' => 'DESC']);

        return $this->render('dashboard/content/market_place/config/index.html.twig', [
            'carriers' => $carriers,
            'pgForm' => $pgForm->createView(),
            'carrierForm' => $carrierForm->createView(),
            'paymentGateways' => $paymentGateways,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{target}/{id}', name: 'app_dashboard_config_remove', methods: ['DELETE'])]
    public function remove(
        Request                $request,
        EntityManagerInterface $manager,
        TranslatorInterface    $translator,
    ): Response
    {
        $target = $request->get('target');
        $id = $request->get('id');

        if ($target === 'carrier') {
            $carrier = $manager->getRepository(StoreCarrier::class)->find($id);
            $manager->remove($carrier);
        }

        if ($target === 'payment_gateway') {
            $paymentGateway = $manager->getRepository(StorePaymentGateway::class)->find($id);

            if ($manager->getRepository(StorePaymentGatewayStore::class)->findOneBy(['gateway' => $paymentGateway])) {
                return $this->json(['success' => false, 'error' => $translator->trans('user.entry.cant_delete')]);
            }

            $manager->remove($paymentGateway);
        }
        $manager->flush();

        return $this->json(['success' => true, 'message' => $translator->trans('user.entry.deleted')], Response::HTTP_OK);
    }

    #[Route('/{target}/{id}', name: 'app_dashboard_config_change', methods: ['GET', 'PUT'])]
    public function change(
        Request                $request,
        EntityManagerInterface $manager,
        TranslatorInterface    $translator,
    ): Response
    {
        $carrier = $paymentGateway = null;

        $target = $request->get('target');
        $id = $request->get('id');

        if ($target === 'payment_gateway') {
            $paymentGateway = $manager->getRepository(StorePaymentGateway::class)->fetch($id);

            if($request->isMethod('PUT')) {
                $payload = $request->getPayload()->all();

                return $this->json([
                    'success' => true,
                    'payload' => $payload,
                    'message' => $translator->trans('user.entry.updated')]);
            }

            return $this->json([
                $paymentGateway,
                $this->generateUrl('app_dashboard_config_change', [
                    'target' => $target,
                    'id' => $id
                ])
            ], Response::HTTP_OK);
        }

        return $this->json([
            'success' => true,
            'message' => $translator->trans('user.entry.updated'),
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @param SluggerInterface $slugger
     * @param CarrierValidatorInterface $carrierValidator
     * @param PaymentGatewayValidatorInterface $paymentGatewayValidator
     * @param ValidatorInterface $validator
     * @param ParameterBagInterface $params
     * @return Response
     */
    #[Route('/{target}', name: 'app_dashboard_config_save', methods: ['POST'])]
    public function save(
        Request                          $request,
        EntityManagerInterface           $manager,
        TranslatorInterface              $translator,
        SluggerInterface                 $slugger,
        CarrierValidatorInterface        $carrierValidator,
        PaymentGatewayValidatorInterface $paymentGatewayValidator,
        ValidatorInterface               $validator,
        ParameterBagInterface            $params
    ): Response
    {
        $payload = $request->getPayload()->all();
        $target = $request->get('target');
        $carrier = $paymentGateway = null;

        if ($target === 'payment_gateway') {
            $inputs = $payload[$target];

            try {
                $this->isCsrfTokenValid($target, $inputs['_token']);
            } catch (\Exception $e) {
                return $this->json(['success' => false, 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }

            unset($inputs['save'], $inputs['_token']);

            $errors = $paymentGatewayValidator->validate($inputs, $validator);

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    return $this->json(['success' => false, 'error' => $error->getMessage()], Response::HTTP_BAD_REQUEST);
                }
            }

            $paymentGateway = new StorePaymentGateway();

            $paymentGateway
                ->setName($inputs['name'])
                ->setSummary($inputs['summary'])
                ->setSlug($slugger->slug($inputs['name'])->lower()->toString())
                ->setHandlerText($inputs['handlerText'])
                ->setIcon($inputs['icon'])
                ->setActive($inputs['active']);

            $manager->persist($paymentGateway);
            $manager->flush();
        }

        if ($target === 'carrier') {
            $inputs = $payload[$target];
            $media = json_decode($inputs['media'], true);

            $file = explode(';base64,', $inputs['attach']);

            $filePath = tempnam(sys_get_temp_dir(), 'UploadedFile');
            $tmpFile = base64_decode($file[1]);
            file_put_contents($filePath, $tmpFile);

            $data = [
                'name' => $inputs['name'],
                'description' => $inputs['description'],
                'linkUrl' => $inputs['linkUrl'],
                'file' => $filePath,
            ];

            $errors = $carrierValidator->validate($data, $validator);

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    return $this->json(['success' => false, 'error' => $error->getMessage()], Response::HTTP_BAD_REQUEST);
                }
            }

            $fileUploader = new FileUploader($params->get('carrier_storage_dir'), $slugger, $manager);

            try {
                $attach = $fileUploader->upload(new UploadedFile($filePath, $media['name'], $media['mime'], null, true))->handle();
            } catch (\Exception $e) {
                return $this->json(['success' => false, 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }

            $carrier = new StoreCarrier();

            $carrier
                ->setDescription($inputs['description'])
                ->setAttach($attach)
                ->setSlug($slugger->slug($inputs['name'])->lower()->toString())
                ->setShippingAmount(0)
                ->setLinkUrl($inputs['linkUrl'])
                ->setEnabled($inputs['enabled']);

            $manager->persist($carrier);
            $manager->flush();
        }

        $template = sprintf('dashboard/content/market_place/config/template/%s.html.twig', $target);

        return $this->json([
            'success' => true,
            'message' => $translator->trans('user.entry.created'),
            'template' => $this->renderView($template, [
                'carrier' => $carrier,
                'pg' => $paymentGateway,
            ])
        ]);
    }
}
