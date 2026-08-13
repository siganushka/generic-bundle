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
                'schema_resort' => true,
            ],
            'serializer' => [
                'form_error_normalizer' => false,
                'knp_pagination_normalizer' => false,
            ],
        ]);
    }

    public function testCustomDoctrineConfig(): void
    {
        $config = [
            'table_prefix' => 'test_',
            'schema_resort' => false,
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [
            ['doctrine' => $config],
        ]);

        static::assertSame($processedConfig['doctrine'], $config);
    }

    public function testCustomSerializerConfig(): void
    {
        $config = [
            'form_error_normalizer' => true,
            'knp_pagination_normalizer' => true,
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [
            ['serializer' => $config],
        ]);

        static::assertSame($processedConfig['serializer'], $config);
    }
}
