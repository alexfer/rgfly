<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\StoreCategory;
use Doctrine\ORM\EntityManagerInterface;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/market-place')]
class SearchController extends AbstractController
{

    public function __construct(ParameterBagInterface $params)
    {
        $this->dsn = $params->get('app.elastic.dsn');
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
     */
    #[Route('/search', name: 'app_market_place_search')]
    public function find(
        Request       $request
    ): Response
    {
        $client = ClientBuilder::create()->setHosts([$this->dsn])->build();
        try {
            $response = $client->getTransport();
        } catch (ClientResponseException|ServerResponseException $e) {
            throw new ServerResponseException($e->getMessage());
        }


        dd($response);
    }
}