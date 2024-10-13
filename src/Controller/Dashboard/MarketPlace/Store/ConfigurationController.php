<?php declare(strict_types=1);

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\StoreCarrier;
use App\Entity\MarketPlace\StorePaymentGateway;
use App\Form\Type\Dashboard\MarketPlace\CarrierType;
use App\Form\Type\Dashboard\MarketPlace\PaymentGatewayType;
use App\Service\FileUploader;
use App\Service\MarketPlace\Dashboard\Configuration\Interface\ConfigurationServiceInterface;
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
     * @param ConfigurationServiceInterface $configuration
     * @return Response
     */
    #[Route('/{tab?}', name: 'app_dashboard_config', methods: ['GET'])]
    public function index(ConfigurationServiceInterface $configuration): Response
    {
        $pgForm = $this->createForm(PaymentGatewayType::class, new StorePaymentGateway());
        $carrierForm = $this->createForm(CarrierType::class, new StoreCarrier());

        $collection = $configuration->collection();

        return $this->render('dashboard/content/market_place/config/index.html.twig', [
            'carriers' => $collection['carriers'],
            'pgForm' => $pgForm->createView(),
            'carrierForm' => $carrierForm->createView(),
            'paymentGateways' => $collection['payment_gateways'],
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param ConfigurationServiceInterface $configuration
     * @return Response
     */
    #[Route('/{target}/{id}', name: 'app_dashboard_config_remove', methods: ['DELETE'])]
    public function remove(
        Request                       $request,
        TranslatorInterface           $translator,
        ConfigurationServiceInterface $configuration,
    ): Response
    {
        $target = $request->get('target');
        $id = $request->get('id');

        $service = $configuration->take($target, $id);

        if (!$service->remove()) {
            return $this->json(['success' => false, 'error' => $translator->trans('user.entry.cant_delete')]);
        }

        return $this->json(['success' => true, 'message' => $translator->trans('user.entry.deleted')], Response::HTTP_OK);
    }

    #[Route('/{target}/{id}', name: 'app_dashboard_config_change', methods: ['GET', 'PUT'])]
    public function change(
        Request                          $request,
        EntityManagerInterface           $manager,
        TranslatorInterface              $translator,
        PaymentGatewayValidatorInterface $paymentGatewayValidator,
        CarrierValidatorInterface        $carrierValidator,
        ValidatorInterface               $validator,
    ): Response
    {
        $carrier = $paymentGateway = null;

        $target = $request->get('target');
        $id = $request->get('id');
        $payload = $request->getPayload()->all();


        if ($target === 'carrier') {


            if ($request->isMethod('PUT')) {
                $inputs = $payload[$target];

                try {
                    $this->isCsrfTokenValid($target, $inputs['_token']);
                } catch (\Exception $e) {
                    return $this->json(['success' => false, 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
                }

                unset($inputs['save'], $inputs['_token']);

                $errors = $carrierValidator->validate([
                    'linkUrl' => $inputs['linkUrl'],
                    'description' => $inputs['description'],
                ], $validator, true);

                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        return $this->json(['success' => false, 'error' => $error->getMessage()], Response::HTTP_BAD_REQUEST);
                    }
                }

                $carrier = $manager->getRepository(StoreCarrier::class)->find($id);

                $carrier->setDescription($inputs['description'])
                    ->setLinkUrl($inputs['linkUrl'])
                    ->setEnabled($inputs['enabled']);

                $manager->persist($carrier);
                $manager->flush();

                return $this->json([
                    'success' => true,
                    'payload' => $payload,
                    'message' => $translator->trans('user.entry.updated')]);
            }

            $carrier = $manager->getRepository(StoreCarrier::class)->fetch($id);

            return $this->json([
                $carrier,
                $this->generateUrl('app_dashboard_config_change', [
                    'target' => $target,
                    'id' => $id
                ])
            ], Response::HTTP_OK);
        }

        if ($target === 'payment_gateway') {

            if ($request->isMethod('PUT')) {
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

                $paymentGateway = $manager->getRepository(StorePaymentGateway::class)->find($id);

                $paymentGateway
                    ->setName($inputs['name'])
                    ->setSummary($inputs['summary'])
                    ->setHandlerText($inputs['handlerText'])
                    ->setIcon($inputs['icon'])
                    ->setActive($inputs['active']);

                $manager->persist($paymentGateway);
                $manager->flush();

                return $this->json([
                    'success' => true,
                    'payload' => $payload,
                    'message' => $translator->trans('user.entry.updated')]);
            }

            $paymentGateway = $manager->getRepository(StorePaymentGateway::class)->fetch($id);

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
        ParameterBagInterface            $params,
        ConfigurationServiceInterface    $configuration,
    ): Response
    {
        $payload = $request->getPayload()->all();
        $target = $request->get('target');

        $service = $configuration->take($target);

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

            $filePath = null;

            if ($inputs['attach']) {
                $file = explode(';base64,', $inputs['attach']);

                $filePath = tempnam(sys_get_temp_dir(), 'UploadedFile');
                $tmpFile = base64_decode($file[1]);
                file_put_contents($filePath, $tmpFile);
            }

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

            if ($filePath) {

                $fileUploader = new FileUploader($params->get('carrier_storage_dir'), $slugger, $manager);

                try {
                    $attach = $fileUploader->upload(new UploadedFile($filePath, $media['name'], $media['mime'], null, true))->handle();
                } catch (\Exception $e) {
                    return $this->json(['success' => false, 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
                }
            }

            $carrier = new StoreCarrier();

            $carrier
                ->setDescription($inputs['description'])
                ->setAttach($attach ?? null)
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
