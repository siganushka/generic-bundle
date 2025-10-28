<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Configuration;
use Siganushka\GenericBundle\Tests\Fixtures\Bar;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
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
                'schema_resort' => true,
                'table_prefix' => null,
                'mapping_override' => [],
            ],
            'serializer' => [
                'entity_mapping' => false,
                'form_error_normalizer' => false,
                'knp_pagination_normalizer' => false,
            ],
        ]);
    }

    public function testCustomDoctrineConfig(): void
    {
        $config = [
            'schema_resort' => false,
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

    public function testCustomSerializerConfig(): void
    {
        $config = [
            'entity_mapping' => true,
            'form_error_normalizer' => true,
            'knp_pagination_normalizer' => true,
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [
            ['serializer' => $config],
        ]);

        static::assertSame($processedConfig['serializer'], $config);
    }

    public function testDoctrineMappingOverrideOriginClassInvalidException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The source class "0" does not exists');

        $config = [
            'mapping_override' => [
                \stdClass::class,
            ],
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [
            ['doctrine' => $config],
        ]);
    }

    public function testDoctrineMappingOverrideTargetClassInvalidException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The target class "non_exists_class" does not exists');

        $config = [
            'mapping_override' => [
                \stdClass::class => 'non_exists_class',
            ],
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [
            ['doctrine' => $config],
        ]);
    }

    public function testDoctrineMappingOverrideTargetClassNonSubclassException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(\sprintf('The target class must be instanceof %s, stdClass given', Foo::class));

        $config = [
            'mapping_override' => [
                Foo::class => \stdClass::class,
            ],
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [
            ['doctrine' => $config],
        ]);
    }

    public function testDoctrineMappingOverrideTargetClassNonSubclass2Exception(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(\sprintf('The target class must be instanceof %s, %s given', Foo::class, Foo::class));

        $config = [
            'mapping_override' => [
                Foo::class => Foo::class,
            ],
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [
            ['doctrine' => $config],
        ]);
    }
}
