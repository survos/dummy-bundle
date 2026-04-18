<?php

declare(strict_types=1);

use Survos\DummyBundle\Command\LoadDummyCommand;
use Survos\DummyBundle\Repository\ImageRepository;
use Survos\DummyBundle\Repository\ProductRepository;
use Survos\DummyBundle\Service\DummyLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('Survos\\DummyBundle\\', '../src/*')
        ->exclude('../src/{Entity}');

    $services->set(ProductRepository::class)
        ->tag('doctrine.repository_service');

    $services->set(ImageRepository::class)
        ->tag('doctrine.repository_service');

    $services->set(DummyLoader::class);
    $services->set(LoadDummyCommand::class)
        ->tag('console.command');
};
