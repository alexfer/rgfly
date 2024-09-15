<?php declare(strict_types=1);

namespace App\Controller\Dashboard\MarketPlace\Store;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/config/overview')]
class ConfigurationController extends AbstractController
{
    #[Route('', name: 'app_dashboard_config')]
    public function index(): Response
    {
        return $this->render('dashboard/content/market_place/config/index.html.twig');
    }
}
