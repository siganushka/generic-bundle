<?php

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
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

        $this->assertInstanceOf(ConfigurationInterface::class, $this->configuration);
        $this->assertInstanceOf(TreeBuilder::class, $treeBuilder);

        $config = $this->processor->processConfiguration($this->configuration, []);

        $this->assertEquals([
            'table_prefix' => null,
            'unescaped_unicode_json_response' => true,
        ], $config);
    }

    public function testCustomConfig(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'table_prefix' => 'app_',
                'unescaped_unicode_json_response' => false,
            ],
        ]);

        $this->assertEquals([
            'table_prefix' => 'app_',
            'unescaped_unicode_json_response' => false,
        ], $config);
    }

    public function testInvalidTablePrefixException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid configuration for path "siganushka_generic.table_prefix": The "1" for table prefix contains illegal character(s).');

        $this->processor->processConfiguration($this->configuration, [
            [
                'table_prefix' => 1,
            ],
        ]);
    }

    public function testInvalidUnescapedUnicodeJsonResponseException(): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->expectExceptionMessage('Invalid type for path "siganushka_generic.unescaped_unicode_json_response". Expected boolean, but got integer.');

        $this->processor->processConfiguration($this->configuration, [
            [
                'unescaped_unicode_json_response' => 1,
            ],
        ]);
    }
}
