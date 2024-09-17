<?php declare(strict_types=1);

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\StoreCarrier;
use App\Entity\MarketPlace\StorePaymentGateway;
use App\Form\Type\Dashboard\MarketPlace\CarrierType;
use App\Form\Type\Dashboard\MarketPlace\PaymentGatewayType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/config/setup')]
class ConfigurationController extends AbstractController
{
    /**
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('', name: 'app_dashboard_config', methods: ['GET'])]
    public function index(
        EntityManagerInterface $manager,
    ): Response
    {
        $pgForm = $this->createForm(PaymentGatewayType::class, new StorePaymentGateway());
        $carrierForm = $this->createForm(CarrierType::class, new StoreCarrier());

        $carriers = $manager->getRepository(StoreCarrier::class)->findAll();
        $paymentGateways = $manager->getRepository(StorePaymentGateway::class)->findAll();

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
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/{target}', name: 'app_dashboard_config_save', methods: ['POST'])]
    public function save(
        Request                $request,
        EntityManagerInterface $manager,
        TranslatorInterface    $translator,
        SluggerInterface       $slugger
    ): Response
    {
        $payload = $request->getPayload()->all();
        $target = $request->get('target');

        if ($target === 'payment_gateway') {
            $inputs = $payload[$target];

            $paymentGateway = new StorePaymentGateway();
            try {
                $paymentGateway
                    ->setName($inputs['name'])
                    ->setSummary($inputs['summary'])
                    ->setSlug($slugger->slug($inputs['name'])->lower()->toString())
                    ->setHandlerText($inputs['handlerText'])
                    ->setIcon($inputs['icon'])
                    ->setActive($inputs['active'] == 1);

                $manager->persist($paymentGateway);
                $manager->flush();
            } catch (\Exception $e) {
                return $this->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        if ($target === 'carrier') {
            $inputs = $payload[$target];
            dd($inputs);
            $carrier = new StoreCarrier();
            try {
                $carrier
                    ->setDescription($inputs['description'])
                    ->setSlug($slugger->slug($inputs['name'])->lower()->toString())
                    ->setShippingAmount(0)
                    ->setLinkUrl($inputs['linkUrl'])
                    ->setEnabled($inputs['enabled'] == 1);

                $manager->persist($carrier);
                $manager->flush();
            } catch (\Exception $e) {
                return $this->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->json([
            'success' => true,
            'message' => $translator->trans('user.entry.created'),
        ]);
    }
}
