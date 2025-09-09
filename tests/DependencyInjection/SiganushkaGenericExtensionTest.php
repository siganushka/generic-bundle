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
            'siganushka_generic.json_request_listener',
            'siganushka_generic.json_response_listener',
            'siganushka_generic.doctrine.nestable_listener',
            'siganushka_generic.doctrine.timestampable_listener',
            'siganushka_generic.doctrine.deletable_listener',
            'siganushka_generic.form.form_type_extension',
            'siganushka_generic.form.button_type_extension',
            'siganushka_generic.form.choice_type_extension',
            'siganushka_generic.form.collection_type_extension',
            'siganushka_generic.serializer.form_error_normalizer',
            'siganushka_generic.serializer.knp_pagination_normalizer',
        ], $container->getServiceIds());

        $jsonRequest = $container->getDefinition('siganushka_generic.json_request_listener');
        static::assertTrue($jsonRequest->hasTag('kernel.event_subscriber'));

        $jsonResponse = $container->getDefinition('siganushka_generic.json_response_listener');
        static::assertTrue($jsonResponse->hasTag('kernel.event_subscriber'));

        $nestable = $container->getDefinition('siganushka_generic.doctrine.nestable_listener');
        static::assertSame([
            ['event' => 'loadClassMetadata'],
        ], $nestable->getTag('doctrine.event_listener'));

        $timestampable = $container->getDefinition('siganushka_generic.doctrine.timestampable_listener');
        static::assertSame([
            ['event' => 'prePersist'],
            ['event' => 'preUpdate'],
        ], $timestampable->getTag('doctrine.event_listener'));

        $deletable = $container->getDefinition('siganushka_generic.doctrine.deletable_listener');
        static::assertSame([
            ['event' => 'onFlush'],
        ], $deletable->getTag('doctrine.event_listener'));

        $formError = $container->getDefinition('siganushka_generic.serializer.form_error_normalizer');
        static::assertTrue($formError->hasTag('serializer.normalizer'));

        $knpPagination = $container->getDefinition('siganushka_generic.serializer.knp_pagination_normalizer');
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
            'serializer' => [
                'form_error_normalizer' => false,
                'knp_pagination_normalizer' => false,
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
            'siganushka_generic.json_request_listener',
            'siganushka_generic.json_response_listener',
            'siganushka_generic.doctrine.mapping_override_listener',
            'siganushka_generic.doctrine.table_prefix_listener',
            'siganushka_generic.doctrine.nestable_listener',
            'siganushka_generic.doctrine.timestampable_listener',
            'siganushka_generic.doctrine.deletable_listener',
            'siganushka_generic.form.form_type_extension',
            'siganushka_generic.form.button_type_extension',
            'siganushka_generic.form.choice_type_extension',
            'siganushka_generic.form.collection_type_extension',
        ], $container->getServiceIds());

        $mappingOverride = $container->getDefinition('siganushka_generic.doctrine.mapping_override_listener');
        static::assertSame([['event' => 'loadClassMetadata']], $mappingOverride->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.mapping_override%', $mappingOverride->getArgument(0));

        $tablePrefix = $container->getDefinition('siganushka_generic.doctrine.table_prefix_listener');
        static::assertSame([['event' => 'loadClassMetadata']], $tablePrefix->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.table_prefix%', $tablePrefix->getArgument(0));
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
