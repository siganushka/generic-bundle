<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';
    public const JSON_ENCODE_OPTIONS = \JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_AMP | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE;

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('siganushka_generic');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
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
                        ->thenInvalid('The "%s" for table prefix contains illegal character(s).')
                    ->end()
                ->end()
                ->scalarNode('datetime_format')
                    ->info('Datetime format configuration, See: https://www.php.net/manual/en/datetime.format.php')
                    ->defaultValue(self::DATETIME_FORMAT)
                ->end()
                ->scalarNode('datetime_timezone')
                    ->info('Datetime timezone configuration, See: https://www.php.net/manual/en/timezones.php')
                    ->defaultNull()
                ->end()
                ->integerNode('json_encode_options')
                    ->info('JSON encode options configuration, See: https://www.php.net/manual/en/function.json-encode.php')
                    ->defaultValue(self::JSON_ENCODE_OPTIONS)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
