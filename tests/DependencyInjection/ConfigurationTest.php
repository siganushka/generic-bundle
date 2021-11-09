<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\Processor;

/**
 * @internal
 * @coversNothing
 */
final class ConfigurationTest extends TestCase
{
    private $configuration;
    private $processor;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testDefaultConfig(): void
    {
        $treeBuilder = $this->configuration->getConfigTreeBuilder();

        static::assertInstanceOf(ConfigurationInterface::class, $this->configuration);
        static::assertInstanceOf(TreeBuilder::class, $treeBuilder);

        $processedConfig = $this->processor->processConfiguration($this->configuration, []);

        static::assertSame($processedConfig, [
            'doctrine' => [
                'table_prefix' => null,
            ],
            'datetime' => [
                'format' => 'Y-m-d H:i:s',
                'timezone' => null,
            ],
            'json' => [
                'encoding_options' => 271,
            ],
            'currency' => [
                'scale' => 2,
                'grouping' => true,
                'rounding_mode' => 6,
                'divisor' => 100,
            ],
        ]);
    }

    public function testCustomDoctrine(): void
    {
        $config = [
            'table_prefix' => 'test_',
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [
            ['doctrine' => $config],
        ]);

        static::assertSame($processedConfig['doctrine'], $config);
    }

    public function testCustomDatetime(): void
    {
        $config = [
            'format' => 'm-d H:i',
            'timezone' => 'RPC',
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [
            ['datetime' => $config],
        ]);

        static::assertSame($processedConfig['datetime'], $config);
    }

    public function testCustomJson(): void
    {
        $config = [
            'encoding_options' => 0,
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [
            ['json' => $config],
        ]);

        static::assertSame($processedConfig['json'], $config);
    }

    public function testCustomJCurrency(): void
    {
        $config = [
            'scale' => 0,
            'grouping' => false,
            'rounding_mode' => 6,
            'divisor' => 1,
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [
            ['currency' => $config],
        ]);

        static::assertSame($processedConfig['currency'], $config);
    }

    // public function testInvalidTablePrefixException(): void
    // {
    //     $this->expectException(InvalidConfigurationException::class);

    //     $this->processor->processConfiguration($this->configuration, [
    //         [
    //             'table_prefix' => 1,
    //         ],
    //     ]);
    // }

    // public function testInvalidJsonEncodeOptionsException(): void
    // {
    //     $this->expectException(InvalidTypeException::class);

    //     $this->processor->processConfiguration($this->configuration, [
    //         [
    //             'json_encode_options' => false,
    //         ],
    //     ]);
    // }
}
