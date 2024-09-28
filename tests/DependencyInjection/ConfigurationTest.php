<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Configuration;
use Siganushka\GenericBundle\Tests\Fixtures\Bar;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
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
                'mapping_override' => [],
            ],
            'form' => [
                'html5_validation' => false,
            ],
        ]);
    }

    public function testCustomDoctrineConfig(): void
    {
        $config = [
            'table_prefix' => 'test_',
            'mapping_override' => [
                Foo::class => Bar::class,
            ],
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
}
