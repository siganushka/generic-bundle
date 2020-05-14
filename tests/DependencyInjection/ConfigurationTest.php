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

        $jsonEncodeOptions = class_exists('Symfony\Component\HttpFoundation\JsonResponse')
            ? \Symfony\Component\HttpFoundation\JsonResponse::DEFAULT_ENCODING_OPTIONS
            : JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

        $config = $this->processor->processConfiguration($this->configuration, []);

        $this->assertEquals([
            'table_prefix' => null,
            'json_encode_options' => $jsonEncodeOptions | JSON_UNESCAPED_UNICODE,
        ], $config);
    }

    public function testCustomConfig(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'table_prefix' => 'app_',
                'json_encode_options' => 0,
            ],
        ]);

        $this->assertEquals([
            'table_prefix' => 'app_',
            'json_encode_options' => 0,
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

    public function testInvalidJsonEncodeOptionsException(): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->expectExceptionMessage('Invalid type for path "siganushka_generic.json_encode_options". Expected int, but got boolean.');

        $this->processor->processConfiguration($this->configuration, [
            [
                'json_encode_options' => false,
            ],
        ]);
    }
}
