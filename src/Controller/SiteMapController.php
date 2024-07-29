<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Entry;
use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SiteMapController extends AbstractController
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/sitemap.{format}', name: 'app_sitemap')]
    public function index(
        Request                $request,
        EntityManagerInterface $em
    ): Response
    {
        $shops = $em->getRepository(Store::class)->findAll();
        $categories = $em->getRepository(StoreCategory::class)->findAll();
        $blogs = $em->getRepository(Entry::class)->findBy(['type' => Entry::TYPE['Blog']], ['updated_at' => 'DESC']);
        $entryCategorise = $em->getRepository(Category::class)->findAll();
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
