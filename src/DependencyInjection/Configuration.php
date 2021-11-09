<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('siganushka_generic');
        $rootNode = $treeBuilder->getRootNode();

        $this->addDoctrineSection($rootNode);
        $this->addDatetimeSection($rootNode);
        $this->addJsonSection($rootNode);
        $this->addCurrencySection($rootNode);

        return $treeBuilder;
    }

    private function addDoctrineSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('doctrine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('table_prefix')
                            ->info('Database table prefix configuration, See: https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/cookbook/sql-table-prefixes.html')
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

    private function addDatetimeSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('datetime')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('format')
                            ->info('Datetime format configuration, See: https://www.php.net/manual/en/datetime.format.php')
                            ->defaultValue('Y-m-d H:i:s')
                        ->end()
                        ->scalarNode('timezone')
                            ->info('Datetime timezone configuration, See: https://www.php.net/manual/en/timezones.php')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addJsonSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('json')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('encoding_options')
                            ->info('JSON encoding options configuration, See: https://www.php.net/manual/en/function.json-encode.php')
                            ->defaultValue(\JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_AMP | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addCurrencySection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('currency')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('scale')->defaultValue(2)->end()
                        ->booleanNode('grouping')->defaultTrue()->end()
                        ->enumNode('rounding_mode')
                            ->defaultValue(\NumberFormatter::ROUND_HALFUP)
                            ->values([
                                \NumberFormatter::ROUND_FLOOR,
                                \NumberFormatter::ROUND_DOWN,
                                \NumberFormatter::ROUND_HALFDOWN,
                                \NumberFormatter::ROUND_HALFEVEN,
                                \NumberFormatter::ROUND_HALFUP,
                                \NumberFormatter::ROUND_UP,
                                \NumberFormatter::ROUND_CEILING,
                            ])
                        ->end()
                        ->integerNode('divisor')->defaultValue(100)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
