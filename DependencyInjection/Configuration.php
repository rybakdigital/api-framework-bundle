<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rybak_digital_api_framework');

        $rootNode
            ->children()
                ->arrayNode('request_formatter')
                    ->children()
                        ->arrayNode('response')
                            ->children()
                                ->arrayNode('headers')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('header')->end()
                                            ->scalarNode('value')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
            ->end();

        return $treeBuilder;
    }
}
