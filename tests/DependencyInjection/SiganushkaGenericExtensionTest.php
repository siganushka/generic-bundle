<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Tests\Fixtures\Bar;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
use Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;
use Symfony\Component\DependencyInjection\Reference;

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
            'siganushka_generic.doctrine.schema_resort_listener',
            'siganushka_generic.doctrine.schema_resort_command',
            'siganushka_generic.form.form_type_extension',
            'siganushka_generic.form.money_type_extension',
            'siganushka_generic.form.button_type_extension',
            'siganushka_generic.form.choice_type_extension',
            'siganushka_generic.form.collection_type_extension',
        ], $container->getServiceIds());

        $jsonRequestListener = $container->getDefinition('siganushka_generic.json_request_listener');
        static::assertTrue($jsonRequestListener->hasTag('kernel.event_subscriber'));

        $jsonResponseListener = $container->getDefinition('siganushka_generic.json_response_listener');
        static::assertTrue($jsonResponseListener->hasTag('kernel.event_subscriber'));

        $nestableListener = $container->getDefinition('siganushka_generic.doctrine.nestable_listener');
        static::assertSame([
            ['event' => 'loadClassMetadata'],
        ], $nestableListener->getTag('doctrine.event_listener'));

        $timestampableListener = $container->getDefinition('siganushka_generic.doctrine.timestampable_listener');
        static::assertSame([
            ['event' => 'prePersist'],
            ['event' => 'preUpdate'],
        ], $timestampableListener->getTag('doctrine.event_listener'));

        $deletableListener = $container->getDefinition('siganushka_generic.doctrine.deletable_listener');
        static::assertSame([
            ['event' => 'onFlush'],
        ], $deletableListener->getTag('doctrine.event_listener'));

        $schemaResortListener = $container->getDefinition('siganushka_generic.doctrine.schema_resort_listener');
        static::assertSame([
            ['event' => 'postGenerateSchemaTable'],
        ], $schemaResortListener->getTag('doctrine.event_listener'));

        $schemaResortCommand = $container->getDefinition('siganushka_generic.doctrine.schema_resort_command');
        static::assertTrue($schemaResortCommand->hasTag('console.command'));

        /** @var Reference */
        $managerRegistry = $schemaResortCommand->getArgument(0);
        static::assertSame('doctrine', (string) $managerRegistry);
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'doctrine' => [
                'schema_resort' => false,
                'table_prefix' => 'test_',
                'mapping_override' => [
                    Foo::class => Bar::class,
                ],
            ],
            'serializer' => [
                'entity_mapping' => true,
                'form_error_normalizer' => true,
                'knp_pagination_normalizer' => true,
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
            'siganushka_generic.form.money_type_extension',
            'siganushka_generic.form.button_type_extension',
            'siganushka_generic.form.choice_type_extension',
            'siganushka_generic.form.collection_type_extension',
            'siganushka_generic.serializer.entity_mapping',
            'siganushka_generic.serializer.form_error_normalizer',
            'siganushka_generic.serializer.knp_pagination_normalizer',
        ], $container->getServiceIds());

        $mappingOverrideListener = $container->getDefinition('siganushka_generic.doctrine.mapping_override_listener');
        static::assertSame([['event' => 'loadClassMetadata']], $mappingOverrideListener->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.mapping_override%', $mappingOverrideListener->getArgument(0));

        $tablePrefixListener = $container->getDefinition('siganushka_generic.doctrine.table_prefix_listener');
        static::assertSame([['event' => 'loadClassMetadata']], $tablePrefixListener->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.table_prefix%', $tablePrefixListener->getArgument(0));

        $entityMapping = $container->getDefinition('siganushka_generic.serializer.entity_mapping');
        static::assertSame(['serializer.mapping.class_metadata_factory', null, 0], $entityMapping->getDecoratedService());

        /** @var Reference */
        $decorated = $entityMapping->getArgument('$decorated');
        static::assertSame('siganushka_generic.serializer.entity_mapping.inner', (string) $decorated);

        /** @var Reference */
        $managerRegistry = $entityMapping->getArgument('$managerRegistry');
        static::assertSame('doctrine', (string) $managerRegistry);

        $formErrorNormalizer = $container->getDefinition('siganushka_generic.serializer.form_error_normalizer');
        static::assertTrue($formErrorNormalizer->hasTag('serializer.normalizer'));

        /** @var Reference */
        $translator = $formErrorNormalizer->getArgument('$translator');
        static::assertSame('translator', (string) $translator);
        static::assertSame(ContainerInterface::IGNORE_ON_INVALID_REFERENCE, $translator->getInvalidBehavior());

        $knpPaginationNormalizer = $container->getDefinition('siganushka_generic.serializer.knp_pagination_normalizer');
        static::assertTrue($knpPaginationNormalizer->hasTag('serializer.normalizer'));
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
