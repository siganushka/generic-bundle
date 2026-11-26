<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
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
            ],
            'serializer' => [
                'form_error_normalizer' => false,
                'knp_pagination_normalizer' => false,
            ],
            'request_format' => [],
        ]);
    }

    public function testCustomDoctrineConfig(): void
    {
        $config = [
            'table_prefix' => 'test_',
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

    public function testCustomRequestFormatConfig(): void
    {
        $config = [
            ['pattern' => '^/api', 'format' => 'json'],
            ['pattern' => '^/server', 'format' => 'xml'],
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [
            ['request_format' => $config],
        ]);

        static::assertSame($processedConfig['request_format'], $config);
    }

    public function testRequestFormatNormalizesPattern(): void
    {
        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [
            ['request_format' => [['pattern' => ['^/api', '^/shop'], 'format' => 'json']]],
        ]);

        static::assertSame([[
            'pattern' => '(?:^/api|^/shop)',
            'format' => 'json',
        ]], $processedConfig['request_format']);
    }

    public function testRequestFormatRequiresPattern(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [
            ['request_format' => [['format' => 'json']]],
        ]);
    }

    public function testRequestFormatRequiresFormat(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [
            ['request_format' => [['pattern' => '^/api']]],
        ]);
    }
}
