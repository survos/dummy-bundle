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
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'dummy:load',
    description: 'Load Product and Image entities from DummyJSON'
)]
final class LoadDummyCommand extends Command
{
    public function __construct(
        private readonly DummyLoader $dummyLoader,
        #[Autowire('%survos_dummy.default_products_url%')]
        private readonly string $defaultProductsUrl,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('source', null, InputOption::VALUE_REQUIRED, 'DummyJSON URL or local file path')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum number of products to import')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Purge existing Product and Image rows before import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $source = (string) ($input->getOption('source') ?: $this->defaultProductsUrl);
        $limit = $input->getOption('limit');
        $limit = is_numeric($limit) ? (int) $limit : null;
        $purge = (bool) $input->getOption('purge');

        $io->title('Load DummyJSON data');
        $io->definitionList(
            ['Source' => $source],
            ['Limit' => $limit ?? 'none'],
            ['Purge' => $purge ? 'yes' : 'no'],
        );

        $counts = $this->dummyLoader->loadProducts($source, $limit, $purge);

        $io->success(sprintf('Imported %d products and %d images.', $counts['products'], $counts['images']));

        return Command::SUCCESS;
    }
}
