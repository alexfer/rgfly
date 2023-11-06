<?php

namespace App\Command;

use App\Repository\CategoryRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand('app:category:slug', 'Generating slug for each category')]
class CategorySlugGenerateCommand extends Command
{
    final const PERFORM = 'perform';
    private CategoryRepository $repository;

    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     * @param CategoryRepository $repository
     */
    public function __construct(SluggerInterface $slugger, CategoryRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->slugger = $slugger;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption(self::PERFORM, null, InputOption::VALUE_NONE, 'Generate');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $qb = $this->repository->createQueryBuilder('c');

        $result = $qb->select('c.id, c.name')
            ->where('c.slug is null')
            ->getQuery()
            ->getArrayResult();

        foreach ($result as $category) {
            $qb->update()
                ->set('c.slug', sprintf("'%s'", $this->slugger->slug($category['name'])->lower()))
                ->where('c.id = :id')
                ->setParameter('id',  $category['id'])
                ->getQuery()
                ->execute();

            print $category['name'] . PHP_EOL;
            print $category['id'] . PHP_EOL;

        }

        $io->success(sprintf('Generated %d slugs.', count($result)));
        return Command::SUCCESS;
    }
}