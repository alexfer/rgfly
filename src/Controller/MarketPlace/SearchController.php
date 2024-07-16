<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\StoreCategory;
use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreProduct;
use Doctrine\ORM\EntityManagerInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\String\u;

#[Route('/market-place')]
class SearchController extends AbstractController
{

    /**
     * @var mixed
     */
    private static mixed $options;

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        self::$options['index'] = $params->get('app.elastic.index');
        $this->client = ClientBuilder::create()->setHosts([$params->get('app.elastic.dsn')])->build();
    }

    /**
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(StoreCategory::class)->findBy(['parent' => null]);
        return $this->render('market_place/search/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/search', name: 'app_market_place_search')]
    public function search(
        Request                $request,
        EntityManagerInterface $em,
        ?UserInterface         $user,
    ): Response
    {
        $products = [];
        $query = $request->query->get('query');

        $category = $request->query->get('category');
        $customer = $em->getRepository(StoreCustomer::class)->findOneBy([
            'member' => $user,
        ]);

        if ($query) {
            $products = $em->getRepository(StoreProduct::class)->search($query, $category);
            $products = $products['data'];
        }

        return $this->render('market_place/search/result.html.twig', [
            'products' => $products,
            'customer' => $customer,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    #[Route('/autocomplete', name: 'app_market_place_search_autocomplete')]
    public function autocomplete(
        Request $request
    ): JsonResponse
    {
        $term = $request->query->get('term');
        $results = [];

        if ($term) {
            $response = $this->client->search(self::params($term));
            $docs = $response['hits']['hits'];

            if (count($docs)) {
                foreach ($docs as $key => $doc) {
                    $product = $doc['_source']['product'];
                    $id = $product['id'];
                    $results[$id] = [
                        'id' => $id,
                        'name' => $doc['_source']['name'],
                        'short_name' => $product['short_name'],
                        'product_slug' => $product['slug'],
                        'store' => $product['store_name'],
                        'store_slug' => $product['store_slug'],
                    ];
                    if ($key > 10) {
                        break;
                    }
                }
            }

        }
        return $this->json([
            'template' => $this->renderView('market_place/search/autocomplete.html.twig', ['results' => $results])
        ], Response::HTTP_OK);
    }

    /**
     * @param string $query
     * @return array
     */
    private static function params(string $query): array
    {
        return [
            'index' => self::$options['index'],
            'body' => [
                'query' => [
                    'wildcard' => [
                        'name' => sprintf('*%s*', $query),
                    ],
                ],
            ],
        ];
    }
}