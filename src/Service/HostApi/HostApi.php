<?php

namespace App\Service\HostApi;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class HostApi implements ApiInterface
{
    /**
     * @var string|null
     */
    private static ?string $apiUrl = null;

    /**
     * @param HttpClientInterface $httpClient
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface     $logger,
        ParameterBagInterface $params
    )
    {
        self::$apiUrl = $params->get("app.hostip.url");
    }

    /**
     * @param string $ipAddress
     * @return string|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function request(string $ipAddress): ?string
    {
        try {
            $request = $this->httpClient->request('GET', self::$apiUrl . '/' . $ipAddress);
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $this->logger->error($e->getMessage());
            return null;
        }

        return $request->getContent();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function determine(string $ipAddress): ?array
    {
        $response = $this->request($ipAddress);

        if(!$response) {
            return null;
        }

        $crawler = new Crawler($response);
        $hostIp = $crawler->filterXPath('//gml:featureMember//Hostip');


        $ip = $hostIp->filterXPath('//ip')->text();
        $city = $hostIp->filterXPath('//gml:name')->text();
        $countryName = $hostIp->filterXPath('//countryName')->text();
        $countryCode = $hostIp->filterXPath('//countryAbbrev')->text();

        $coordinates = $hostIp->filterXPath('//ipLocation//gml:pointProperty//gml:Point//gml:coordinates')->text();
        $coordinates = explode(",", $coordinates);
        $coordinates = array_reverse($coordinates);

        $result = [
            'ip' => $ip,
            'city' => $city,
            'countryName' => $countryName,
            'countryCode' => $countryCode,
            'coordinates' => [
                'lat' => $coordinates[0],
                'lng' => $coordinates[1],
            ],
        ];

        return $result;
    }
}
