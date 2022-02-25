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
    private ?ConfigurationInterface $configuration = null;
    private ?Processor $processor = null;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    protected function tearDown(): void
    {
        $this->configuration = null;
        $this->processor = null;
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
}
