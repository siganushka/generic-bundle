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
            'form' => [
                'html5_validation' => false,
            ],
            'json' => [
                'encoding_options' => 271,
            ],
            'currency' => [
                'decimals' => 2,
                'dec_point' => '.',
                'thousands_sep' => ',',
                'divisor' => 100,
            ],
        ]);
    }

    public function testCustomDoctrineConfig(): void
    {
        $config = [
            'table_prefix' => 'test_',
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [
            ['doctrine' => $config],
        ]);

        static::assertSame($processedConfig['doctrine'], $config);
    }

    public function testCustomFormConfig(): void
    {
        $config = [
            'html5_validation' => true,
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [
            ['form' => $config],
        ]);

        static::assertSame($processedConfig['form'], $config);
    }

    public function testCustomJsonConfig(): void
    {
        $config = [
            'encoding_options' => 0,
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [
            ['json' => $config],
        ]);

        static::assertSame($processedConfig['json'], $config);
    }

    public function testCustomCurrencyConfig(): void
    {
        $config = [
            'decimals' => 0,
            'dec_point' => '_',
            'thousands_sep' => '_',
            'divisor' => 1,
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, [
            ['currency' => $config],
        ]);

        static::assertSame($processedConfig['currency'], $config);
    }
}
