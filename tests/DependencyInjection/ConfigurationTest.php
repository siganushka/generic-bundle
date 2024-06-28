<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $configuration = new Configuration();
        $treeBuilder = $configuration->getConfigTreeBuilder();

        static::assertInstanceOf(ConfigurationInterface::class, $configuration);
        static::assertInstanceOf(TreeBuilder::class, $treeBuilder);

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration($configuration, []);

        static::assertSame($processedConfig, [
            'doctrine' => [
                'table_prefix' => null,
                'entity_to_superclass' => [],
            ],
            'form' => [
                'html5_validation' => false,
            ],
            'currency' => [
                'divisor' => 100,
                'decimals' => 2,
                'dec_point' => '.',
                'thousands_sep' => ',',
            ],
        ]);
    }

    public function testCustomDoctrineConfig(): void
    {
        $config = [
            'table_prefix' => 'test_',
            'entity_to_superclass' => ['foo', 'bar', 'baz'],
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [
            ['doctrine' => $config],
        ]);

        static::assertSame($processedConfig['doctrine'], $config);
    }

    public function testCustomFormConfig(): void
    {
        $config = [
            'html5_validation' => true,
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [
            ['form' => $config],
        ]);

        static::assertSame($processedConfig['form'], $config);
    }

    public function testCustomCurrencyConfig(): void
    {
        $config = [
            'decimals' => 0,
            'dec_point' => '_',
            'thousands_sep' => '_',
            'divisor' => 1,
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [
            ['currency' => $config],
        ]);

        static::assertSame($processedConfig['currency'], $config);
    }
}
