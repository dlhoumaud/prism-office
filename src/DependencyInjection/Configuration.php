<?php

declare(strict_types=1);

namespace PrismOffice\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration du bundle PrismOffice
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('prism_office');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->info('Enable PrismOffice interface (only in debug mode)')
                    ->defaultValue('%kernel.debug%')
                ->end()
                ->scalarNode('route_prefix')
                    ->info('Route prefix for PrismOffice interface')
                    ->defaultValue('/prism')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
