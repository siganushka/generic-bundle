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

        $config = $this->processor->processConfiguration($this->configuration, []);

        static::assertSame($config, [
            'table_prefix' => null,
            'datetime_format' => Configuration::DATETIME_FORMAT,
            'datetime_timezone' => null,
            'json_encode_options' => Configuration::JSON_ENCODE_OPTIONS,
        ]);
    }

    public function testCustomConfig(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'table_prefix' => 'test_',
                'datetime_format' => 'm-d H:i',
                'datetime_timezone' => 'RPC',
                'json_encode_options' => 15,
            ],
        ]);

        static::assertSame($config, [
            'table_prefix' => 'test_',
            'datetime_format' => 'm-d H:i',
            'datetime_timezone' => 'RPC',
            'json_encode_options' => 15,
        ]);
    }

    public function testInvalidTablePrefixException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [
            [
                'table_prefix' => 1,
            ],
        ]);
    }

    public function testInvalidJsonEncodeOptionsException(): void
    {
        $this->expectException(InvalidTypeException::class);

        $this->processor->processConfiguration($this->configuration, [
            [
                'json_encode_options' => false,
            ],
        ]);
    }
}
