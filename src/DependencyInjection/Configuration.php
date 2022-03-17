<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_generic');
        /** @var ArrayNodeDefinition */
        $rootNode = $treeBuilder->getRootNode();

        $this->addDoctrineSection($rootNode);
        $this->addJsonSection($rootNode);
        $this->addCurrencySection($rootNode);

        return $treeBuilder;
    }

    private function addDoctrineSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('doctrine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('table_prefix')
                            ->defaultNull()
                            ->validate()
                                ->ifTrue(function ($v) {
                                    if (null === $v) {
                                        return false;
                                    }

                                    if (!\is_string($v)) {
                                        return true;
                                    }

                                    return !preg_match('/^[a-zA-Z0-9_]+$/', $v);
                                })
                                ->thenInvalid('The "%s" for doctrine.table_prefix contains illegal character(s).')
        ;
    }

    private function addJsonSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('json')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('encoding_options')
                            ->defaultValue(\JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_AMP | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addCurrencySection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('currency')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('decimals')->defaultValue(2)->end()
                        ->scalarNode('dec_point')->defaultValue('.')->end()
                        ->scalarNode('thousands_sep')->defaultValue(',')->end()
                        ->integerNode('divisor')->defaultValue(100)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
