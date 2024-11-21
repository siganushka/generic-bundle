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
                                ->ifTrue(static fn (mixed $v): bool => \is_string($v) && !preg_match('/^[a-zA-Z0-9_]+$/', $v))
                                ->thenInvalid('The "%s" for doctrine.table_prefix contains illegal character(s).')
                            ->end()
                        ->end()
                        ->arrayNode('mapping_override')
                            ->useAttributeAsKey('source')
                            ->prototype('scalar')
                                ->cannotBeEmpty()
                            ->end()
                            ->validate()
                                ->always()
                                ->then(static function (array $value) {
                                    foreach ($value as $source => $target) {
                                        $source = (string) $source;
                                        $target = (string) $target;

                                        if (!class_exists($source)) {
                                            throw new \InvalidArgumentException(\sprintf('The source class "%s" does not exists.', $source));
                                        }

                                        if (!class_exists($target)) {
                                            throw new \InvalidArgumentException(\sprintf('The target class "%s" does not exists.', $target));
                                        }

                                        if (!is_subclass_of($target, $source, true)) {
                                            throw new \InvalidArgumentException(\sprintf('The target class must be instanceof '.$source.', %s given.', $target));
                                        }
                                    }

                                    return $value;
                                })
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
                        ->booleanNode('html5_validation')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
