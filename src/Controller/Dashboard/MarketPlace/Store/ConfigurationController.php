<?php declare(strict_types=1);

namespace App\Controller\Dashboard\MarketPlace\Store;

use App\Entity\MarketPlace\StoreCarrier;
use App\Entity\MarketPlace\StorePaymentGateway;
use App\Form\Type\Dashboard\MarketPlace\PaymentGatewayType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/config/setup')]
class ConfigurationController extends AbstractController
{
    #[Route('/{tab}', name: 'app_dashboard_config', defaults: ['tab' => 'overview'])]
    public function index(
        EntityManagerInterface $manager,
    ): Response
    {
        $form = $this->createForm(PaymentGatewayType::class, new StorePaymentGateway());
        $carriers = $manager->getRepository(StoreCarrier::class)->findAll();
        $paymentGateways = $manager->getRepository(StorePaymentGateway::class)->findAll();
        return $this->render('dashboard/content/market_place/config/index.html.twig', [
            'carriers' => $carriers,
            'pgForm' => $form->createView(),
            'paymentGateways' => $paymentGateways,
        ]);
    }
}
