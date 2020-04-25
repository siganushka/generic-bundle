<?php

namespace Siganushka\GenericBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('siganushka_generic');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
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
                        ->thenInvalid('The "%s" for table prefix contains illegal character(s).')
                    ->end()
                ->end()
                ->booleanNode('unescaped_unicode_json_response')
                    ->defaultTrue()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
