<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Command;

use Survos\DummyBundle\Service\DummyLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'dummy:load',
    description: 'Load the full DummyJSON graph into Doctrine entities'
)]
final class LoadDummyCommand extends Command
{
    public function __construct(
        private readonly DummyLoader $dummyLoader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('purge', null, InputOption::VALUE_NONE, 'Purge existing rows before import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $purge = (bool) $input->getOption('purge');

        $io->title('Load DummyJSON data');
        $io->definitionList([
            'Purge' => $purge ? 'yes' : 'no',
        ]);

        $counts = $this->dummyLoader->loadAll($purge);

        $io->success(sprintf(
            'Loaded %d users, %d posts, %d comments, %d products, %d reviews, and %d images.',
            $counts['users'],
            $counts['posts'],
            $counts['comments'],
            $counts['products'],
            $counts['reviews'],
            $counts['images'],
        ));

        return Command::SUCCESS;
    }
}
