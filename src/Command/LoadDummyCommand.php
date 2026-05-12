<?php

declare(strict_types=1);

namespace Survos\DummyBundle\Command;

use Survos\DummyBundle\Service\DummyLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('dummy:load', 'Load the full DummyJSON graph into Doctrine entities')]
final class LoadDummyCommand
{
    public function __construct(
        private readonly DummyLoader $dummyLoader,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option('Purge existing rows before import')] bool $purge = false,
    ): int {
        $io->title('Load DummyJSON data');
        $io->definitionList(['Purge' => $purge ? 'yes' : 'no']);

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
