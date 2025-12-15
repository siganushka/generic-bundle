<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Tests\Fixtures\Bar;
use Siganushka\GenericBundle\Tests\Fixtures\Foo;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainerWithConfig();

        static::assertNull($container->getParameter('siganushka_generic.doctrine.table_prefix'));
        static::assertSame([], $container->getParameter('siganushka_generic.doctrine.mapping_override'));

        static::assertSame([
            'service_container',
            'siganushka_generic.json_response_listener',
            'siganushka_generic.knp_paginator_decorator',
            'siganushka_generic.doctrine.nestable_listener',
            'siganushka_generic.doctrine.timestampable_listener',
            'siganushka_generic.doctrine.deletable_listener',
            'siganushka_generic.doctrine.schema_resort_listener',
            'siganushka_generic.doctrine.schema_resort_command',
            'siganushka_generic.form.controller',
            'siganushka_generic.form.money_type_extension',
            'siganushka_generic.form.button_type_extension',
            'siganushka_generic.form.choice_type_extension',
            'siganushka_generic.form.collection_type_extension',
            'siganushka_generic.serializer.entity_metadata_factory',
            'siganushka_generic.serializer.dump_serialization_command',
        ], $container->getServiceIds());

        $jsonResponseListener = $container->getDefinition('siganushka_generic.json_response_listener');
        static::assertTrue($jsonResponseListener->hasTag('kernel.event_subscriber'));

        $knpPaginatorDecorator = $container->getDefinition('siganushka_generic.knp_paginator_decorator');
        static::assertSame(['knp_paginator', null, 0], $knpPaginatorDecorator->getDecoratedService());
        static::assertSame('%knp_paginator.page_name%', $knpPaginatorDecorator->getArgument('$pageName'));

        /** @var Reference */
        $decorated = $knpPaginatorDecorator->getArgument('$decorated');
        static::assertSame('siganushka_generic.knp_paginator_decorator.inner', $decorated->__toString());

        /** @var Reference */
        $requestStack = $knpPaginatorDecorator->getArgument('$requestStack');
        static::assertSame('request_stack', $requestStack->__toString());

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
        $managerRegistry = $schemaResortCommand->getArgument('$managerRegistry');
        static::assertSame('doctrine', $managerRegistry->__toString());

        $formController = $container->getDefinition('siganushka_generic.form.controller');
        static::assertTrue($formController->hasTag('controller.service_arguments'));

        $formTypes = $formController->getArgument('$formTypes');
        static::assertInstanceOf(TaggedIteratorArgument::class, $formTypes);
        static::assertSame('form.type', $formTypes->getTag());

        $entityMapping = $container->getDefinition('siganushka_generic.serializer.entity_metadata_factory');
        static::assertSame(['serializer.mapping.class_metadata_factory', null, 0], $entityMapping->getDecoratedService());

        /** @var Reference */
        $decorated = $entityMapping->getArgument('$decorated');
        static::assertSame('siganushka_generic.serializer.entity_metadata_factory.inner', $decorated->__toString());

        /** @var Reference */
        $registry = $entityMapping->getArgument('$registry');
        static::assertSame('doctrine', $registry->__toString());

        $dumpSerializationCommand = $container->getDefinition('siganushka_generic.serializer.dump_serialization_command');
        static::assertTrue($dumpSerializationCommand->hasTag('console.command'));

        /** @var Reference */
        $managerRegistry = $dumpSerializationCommand->getArgument('$managerRegistry');
        static::assertSame('doctrine', $managerRegistry->__toString());

        /** @var Reference */
        $metadataFactory = $dumpSerializationCommand->getArgument('$metadataFactory');
        static::assertSame('serializer.mapping.class_metadata_factory', $metadataFactory->__toString());

        static::assertSame('%kernel.project_dir%/config/serializer', $dumpSerializationCommand->getArgument('$serializationDir'));
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
                'form_error_normalizer' => true,
                'knp_pagination_normalizer' => true,
            ],
        ];

        $container = $this->createContainerWithConfig($configs, true);

        static::assertSame('test_', $container->getParameter('siganushka_generic.doctrine.table_prefix'));
        static::assertSame([Foo::class => Bar::class], $container->getParameter('siganushka_generic.doctrine.mapping_override'));

        static::assertSame([
            'service_container',
            'siganushka_generic.json_response_listener',
            'siganushka_generic.knp_paginator_decorator',
            'siganushka_generic.doctrine.mapping_override_listener',
            'siganushka_generic.doctrine.table_prefix_listener',
            'siganushka_generic.doctrine.nestable_listener',
            'siganushka_generic.doctrine.timestampable_listener',
            'siganushka_generic.doctrine.deletable_listener',
            'siganushka_generic.form.controller',
            'siganushka_generic.form.csrf_type_extension',
            'siganushka_generic.form.money_type_extension',
            'siganushka_generic.form.button_type_extension',
            'siganushka_generic.form.choice_type_extension',
            'siganushka_generic.form.collection_type_extension',
            'siganushka_generic.serializer.entity_metadata_factory',
            'siganushka_generic.serializer.dump_serialization_command',
            'siganushka_generic.serializer.form_error_normalizer',
            'siganushka_generic.serializer.knp_pagination_normalizer',
        ], $container->getServiceIds());

        $mappingOverrideListener = $container->getDefinition('siganushka_generic.doctrine.mapping_override_listener');
        static::assertSame([['event' => 'loadClassMetadata']], $mappingOverrideListener->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.mapping_override%', $mappingOverrideListener->getArgument('$mappingOverride'));

        $tablePrefixListener = $container->getDefinition('siganushka_generic.doctrine.table_prefix_listener');
        static::assertSame([['event' => 'loadClassMetadata']], $tablePrefixListener->getTag('doctrine.event_listener'));
        static::assertSame('%siganushka_generic.doctrine.table_prefix%', $tablePrefixListener->getArgument('$prefix'));

        $csrfTypeExtension = $container->getDefinition('siganushka_generic.form.csrf_type_extension');
        static::assertTrue($csrfTypeExtension->hasTag('form.type_extension'));
        static::assertSame(-128, $csrfTypeExtension->getTag('form.type_extension')[0]['priority']);

        /** @var Reference */
        $requestStack = $csrfTypeExtension->getArgument('$requestStack');
        static::assertSame('request_stack', $requestStack->__toString());

        $formErrorNormalizer = $container->getDefinition('siganushka_generic.serializer.form_error_normalizer');
        static::assertTrue($formErrorNormalizer->hasTag('serializer.normalizer'));

        /** @var Reference */
        $translator = $formErrorNormalizer->getArgument('$translator');
        static::assertSame('translator', $translator->__toString());
        static::assertSame(ContainerInterface::IGNORE_ON_INVALID_REFERENCE, $translator->getInvalidBehavior());

        $knpPaginationNormalizer = $container->getDefinition('siganushka_generic.serializer.knp_pagination_normalizer');
        static::assertTrue($knpPaginationNormalizer->hasTag('serializer.normalizer'));
    }

    private function createContainerWithConfig(array $config = [], bool $csrf = false): ContainerBuilder
    {
        $parameters = [
            'form.type_extension.csrf.enabled' => $csrf,
        ];

        $extension = new SiganushkaGenericExtension();

        $container = new ContainerBuilder(new EnvPlaceholderParameterBag($parameters));
        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias(), $config);

        $container->getCompilerPassConfig()->setOptimizationPasses([new ResolveChildDefinitionsPass()]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
