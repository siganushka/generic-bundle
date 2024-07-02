<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @psalm-suppress UndefinedInterfaceMethod
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('siganushka_generic');
        /** @var ArrayNodeDefinition */
        $rootNode = $treeBuilder->getRootNode();

        $this->addDoctrineSection($rootNode);
        $this->addFormSection($rootNode);

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
                                ->ifTrue(function (mixed $v) {
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
                        ->arrayNode('entity_to_superclass')
                            ->scalarPrototype()->end()
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
}
