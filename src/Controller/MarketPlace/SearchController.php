<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\StoreCategory;
use Doctrine\ORM\EntityManagerInterface;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place')]
class SearchController extends AbstractController
{

    private mixed $options;

    public function __construct(ParameterBagInterface $params)
    {
        $this->options['dsn'] = $params->get('app.elastic.dsn');
        $this->options['index'] = $params->get('app.elastic.index');
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
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    #[Route('/search/{query?}', name: 'app_market_place_search')]
    public function find(
        Request $request
    ): Response
    {
        $query = $request->get('query');
        $client = ClientBuilder::create()->setHosts([$this->options['dsn']])->build();
        $params = [
            'index' => $this->options['index'],
            'body' => [
                'query' => [
                    'match' => [
                        'name' => $query,
                    ],
                ],
            ],
        ];
        $results = $client->search($params);
        $doc   = $results['hits']['hits'];
        dd($doc[0]['_source']);
    }
}