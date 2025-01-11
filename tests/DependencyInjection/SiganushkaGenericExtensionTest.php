<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Tests\Fixtures\Bar;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
use Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;

final class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainerWithConfig([]);

        static::assertSame([
            'kernel.project_dir' => __DIR__,
            'siganushka_generic.doctrine.table_prefix' => null,
            'siganushka_generic.doctrine.mapping_override' => [],
        ], $container->getParameterBag()->all());

        static::assertSame([
            'service_container',
            'siganushka_generic.listener.json_request',
            'siganushka_generic.listener.json_response',
            'siganushka_generic.doctrine.listener.timestampable',
            'siganushka_generic.form.type_extension.collection',
            'siganushka_generic.form.type_extension.button',
            'siganushka_generic.form.type.identifier_entity',
            'siganushka_generic.serializer.normalizer.knp_pagination',
        ], $container->getServiceIds());

        $jsonRequest = $container->getDefinition('siganushka_generic.listener.json_request');
        static::assertTrue($jsonRequest->hasTag('kernel.event_subscriber'));

        $jsonResponse = $container->getDefinition('siganushka_generic.listener.json_response');
        static::assertTrue($jsonResponse->hasTag('kernel.event_subscriber'));

        $listenerTagAttributes = [
            ['event' => 'prePersist'],
            ['event' => 'preUpdate'],
        ];

        $timestampable = $container->getDefinition('siganushka_generic.doctrine.listener.timestampable');
        static::assertSame($listenerTagAttributes, $timestampable->getTag('doctrine.event_listener'));

        $knpPagination = $container->getDefinition('siganushka_generic.serializer.normalizer.knp_pagination');
        static::assertTrue($knpPagination->hasTag('serializer.normalizer'));
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'doctrine' => [
                'table_prefix' => 'test_',
                'mapping_override' => [
                    Foo::class => Bar::class,
                ],
            ],
            'form' => [
                'html5_validation' => false,
            ],
        ];

        $container = $this->createContainerWithConfig($configs);

        static::assertSame([
            'kernel.project_dir' => __DIR__,
            'siganushka_generic.doctrine.table_prefix' => 'test_',
            'siganushka_generic.doctrine.mapping_override' => [Foo::class => Bar::class],
        ], $container->getParameterBag()->all());

        static::assertSame([
            'service_container',
            'siganushka_generic.listener.json_request',
            'siganushka_generic.listener.json_response',
            'siganushka_generic.doctrine.listener.mapping_override',
            'siganushka_generic.doctrine.listener.table_prefix',
            'siganushka_generic.doctrine.listener.timestampable',
            'siganushka_generic.form.type_extension.collection',
            'siganushka_generic.form.type_extension.button',
            'siganushka_generic.form.type_extension.html5_validation',
            'siganushka_generic.form.type.identifier_entity',
            'siganushka_generic.serializer.normalizer.knp_pagination',
        ], $container->getServiceIds());

        $mappingOverride = $container->getDefinition('siganushka_generic.doctrine.listener.mapping_override');
        static::assertSame([['event' => 'loadClassMetadata']], $mappingOverride->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.mapping_override%', $mappingOverride->getArgument(0));

        $tablePrefix = $container->getDefinition('siganushka_generic.doctrine.listener.table_prefix');
        static::assertSame([['event' => 'loadClassMetadata']], $tablePrefix->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.table_prefix%', $tablePrefix->getArgument(0));

        $html5Validation = $container->getDefinition('siganushka_generic.form.type_extension.html5_validation');
        static::assertTrue($html5Validation->hasTag('form.type_extension'));
    }

    public function testGetPublicDirectory(): void
    {
        $publicDirectory = SiganushkaGenericExtension::getPublicDirectory($this->createContainerWithConfig());
        static::assertSame(__DIR__.'/public', $publicDirectory);

        $publicDirectory = SiganushkaGenericExtension::getPublicDirectory($this->createContainerWithConfig([], __DIR__.'/../Fixtures/app'));
        static::assertSame(__DIR__.'/../Fixtures/app/html', $publicDirectory);
    }

    private function createContainerWithConfig(array $config = [], string $projectDir = __DIR__): ContainerBuilder
    {
        $extension = new SiganushkaGenericExtension();

        $container = new ContainerBuilder(new EnvPlaceholderParameterBag(['kernel.project_dir' => $projectDir]));
        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias(), $config);

        $container->getCompilerPassConfig()->setOptimizationPasses([new ResolveChildDefinitionsPass()]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
