<?php

namespace App\Command;

use App\Entity\MarketPlace\StoreProduct;
use Doctrine\ORM\EntityManagerInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:populate:indices',
    description: 'Populate indexes into elasticsearch',
    aliases: ['app:elasticsearch-indexing'],
    hidden: false,
)]
class ElasticPopulateCommand extends Command
{
    /**
     * @var array
     */
    private array $options;


    /**
     * @param EntityManagerInterface $manager
     * @param ParameterBagInterface $params
     */
    public function __construct(
        private readonly EntityManagerInterface $manager,
        ParameterBagInterface                   $params
    )
    {
        $this->options['dsn'] = $params->get('app.elastic.dsn');
        $this->options['index'] = $params->get('app.elastic.index');
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption('reset', null, InputOption::VALUE_NONE, 'Delete an index')
            ->addOption('create', null, InputOption::VALUE_NONE, 'Create a new index')
            ->addOption('populate', null, InputOption::VALUE_NONE, 'Populate indices');
    }

    /**
     * @return Client
     * @throws AuthenticationException
     */
    private function client(): Client
    {
        return ClientBuilder::create()->setHosts([$this->options['dsn']])->build();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $client = $this->client();
        $index = $this->options['index'];

        $helper = $this->getHelper('question');
        $reset = $input->getOption('reset');
        $create = $input->getOption('create');
        $populate = $input->getOption('populate');

        if ($reset) {
            $question = new ConfirmationQuestion('Are you sure?(y|n)', false,
                '/^(y|j)/i');
            if ($helper->ask($input, $output, $question) == 'y') {

                $this->reset($client, $index);
                $io->success(strtoupper('deleted'));
                return Command::SUCCESS;
            }
        }

        if ($create) {
            $question = new ConfirmationQuestion('Are you sure?(y|n)', false,
                '/^(y|j)/i');
            if ($helper->ask($input, $output, $question) == 'y') {
                $result = $this->create($client, $index);
                $io->success($result);
                return Command::SUCCESS;
            }
        }

        if($populate) {
            $queryBuilder = $this->manager
                ->getRepository(StoreProduct::class)
                ->createQueryBuilder('p');

            $products = $queryBuilder->select(['p.id', 'p.slug', 'p.name', 'p.short_name'])
                ->where('p.deleted_at IS NULL')
                ->andWhere('p.quantity > 0')
                ->getQuery()
                ->getArrayResult();

            foreach ($products as $product) {
                try {
                    $client->create([
                        'id' => $product['id'],
                        'index' => $index,
                        'client' => [
                            'future' => 'lazy'
                        ],
                        'body' => [
                            'name' => $product['name'],
                            'product' => [
                                'id' => $product['id'],
                                'short_name' => $product['short_name'],
                                'slug' => $product['slug'],
                            ]
                        ]
                    ]);
                    $io->writeln(sprintf('Added [%s] to elasticsearch]', $product['name']));
                } catch (ClientResponseException|MissingParameterException|ServerResponseException $e) {
                    $io->error($e->getMessage());
                }
            }

            return Command::SUCCESS;
        }
        $io->error('Invalid command. Try --help');
        return Command::INVALID;
    }

    /**
     * @param Client $client
     * @param $index
     * @return void
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    private function reset(Client $client, $index): void
    {
        $client->indices()->delete(['index' => $index]);
    }

    /**
     * @param Client $client
     * @param $index
     * @return string[]
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    private function create(Client $client, $index): array
    {
        $exists = $client->indices()->exists(['index' => $index]);

        $result = $exists->asBool();
        if ($result) {
            return ['message' => 'Already exists'];
        }

        $options = [
            'index' => $index,
            'body' => [
                'settings' => [
                    'analysis' => [
                        'filter' => [
                            'shingle' => [
                                'type' => 'shingle'
                            ]
                        ],
                        'analyzer' => [
                            'reuters' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase', 'stop', 'kstem']
                            ]
                        ]
                    ],
                ],
                'mappings' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        'name' => [
                            'type' => 'text',
                            'analyzer' => 'reuters',
                        ],
                    ],
                ],
            ],
        ];

        $client->indices()->create($options);
        return ['message' => 'Created'];
    }
}