<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\ControllerTrait;
use App\Entity\Category;
use App\Entity\Entry;
use App\Entity\MarketPlace\{Store, StoreCategory};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SiteMapController extends AbstractController
{
    use ControllerTrait;

    /**
     * @return Response
     */
    #[Route('/sitemap.{format}', name: 'app_sitemap')]
    public function index(): Response
    {
        $shops = $this->em->getRepository(Store::class)->findAll();
        $categories = $this->em->getRepository(StoreCategory::class)->findAll();
        $blogs = $this->em->getRepository(Entry::class)->findBy(['type' => Entry::TYPE['Blog']], ['updated_at' => 'DESC']);
        $entryCategorise = $this->em->getRepository(Category::class)->findAll();
        $urls = [];

        foreach ($entryCategorise as $category) {
            $urls['entryCategorise'][] = [
                'loc' => $this->generateUrl(
                    'app_blog_view',
                    ['slug' => $category->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => $category->getCreatedAt()->format('Y-m-d'),
                'changefreq' => 'yearly',
                'priority' => '1.0',
            ];
        }

        foreach ($blogs as $blog) {
            $urls['blogs'][] = [
                'loc' => $this->generateUrl(
                    'app_blog_view',
                    ['slug' => $blog->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => $blog->getUpdatedAt()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '1.0',
            ];
        }

        foreach ($categories as $category) {
            $urls['categories'][] = [
                'loc' => $this->generateUrl(
                    'app_market_place_market',
                    ['slug' => $category->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => $category->getCreatedAt()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '1.0',
            ];
        }

        foreach ($shops as $shop) {
            $urls['shops'][] = [
                'loc' => $this->generateUrl(
                    'app_market_place_market',
                    ['slug' => $shop->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => $shop->getCreatedAt()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];
        }

        $urls = $urls['entryCategorise'] + $urls['categories'] + $urls['shops'] + $urls['blogs'];

        $response = new Response(
            $this->renderView('sitemap/index.html.twig', ['urls' => $urls]),
            200
        );

        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
