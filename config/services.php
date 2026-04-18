<?php

declare(strict_types=1);

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Survos\DummyBundle\Command\LoadDummyCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('Survos\\DummyBundle\\', '../src/*')
        ->exclude('../src/{Entity}');

    $services->instanceof(ServiceEntityRepository::class)
        ->tag('doctrine.repository_service');

    $services->set(LoadDummyCommand::class)
        ->tag('console.command');
};
