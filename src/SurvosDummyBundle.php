<?php

declare(strict_types=1);

namespace Survos\DummyBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class SurvosDummyBundle extends AbstractBundle
{
    protected string $extensionAlias = 'survos_dummy';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('default_products_url')->defaultValue('https://dummyjson.com/products?limit=200')->end()
            ->end();
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'SurvosDummyBundle' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => \dirname(__DIR__).'/src/Entity',
                        'prefix' => 'Survos\\DummyBundle\\Entity',
                        'alias' => 'Dummy',
                    ],
                ],
            ],
        ]);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        $builder->setParameter('survos_dummy.default_products_url', $config['default_products_url']);
    }
}
