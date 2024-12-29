<?php declare(strict_types=1);

namespace Essence\Command\Elastic;

use Doctrine\ORM\QueryBuilder;
use Elastic\Elasticsearch\Client;
use Symfony\Component\Console\Style\SymfonyStyle;

interface ElasticServiceInterface
{
    /**
     * @param Client $client
     * @param string $index
     * @return array
     */
    public function create(Client $client, string $index): array;

    /**
     * @param Client $client
     * @param string $index
     * @return void
     */
    public function reset(Client $client, string $index): void;

    /**
     * @param QueryBuilder $builder
     * @param SymfonyStyle $io
     * @param Client $client
     * @param string $index
     * @return void
     */
    public function build(QueryBuilder $builder, SymfonyStyle $io, Client $client, string $index): void;
}