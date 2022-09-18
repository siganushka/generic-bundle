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
        $this->addFormSection($rootNode);
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
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addFormSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('html5_validation')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addJsonSection(ArrayNodeDefinition $rootNode): void
    {
        $encodingOptions = \JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_AMP | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE;

        $rootNode
            ->children()
                ->arrayNode('json')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('encoding_options')->defaultValue($encodingOptions)->end()
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
                        ->integerNode('divisor')->defaultValue(100)->end()
                        ->integerNode('decimals')->defaultValue(2)->end()
                        ->scalarNode('dec_point')->defaultValue('.')->end()
                        ->scalarNode('thousands_sep')->defaultValue(',')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
