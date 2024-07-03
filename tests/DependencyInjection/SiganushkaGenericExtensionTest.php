<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\Contracts\Doctrine\EventListener\SortableListener;
use Siganushka\Contracts\Doctrine\EventListener\TablePrefixListener;
use Siganushka\Contracts\Doctrine\EventListener\TimestampableListener;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Doctrine\EventListener\EntityToSuperclassListener;
use Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainerWithConfig([]);

        static::assertTrue($container->hasDefinition('siganushka_generic.listener.json_request'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.json_response'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.form_error'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.resize_image'));
        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.entity_to_superclass'));
        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.table_prefix'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.timestampable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.sortable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.form.type_extension.disable_html5_validation'));
        static::assertTrue($container->hasDefinition('siganushka_generic.serializer.normalizer.knp_pagination'));

        $jsonRequestDef = $container->getDefinition('siganushka_generic.listener.json_request');
        static::assertTrue($jsonRequestDef->hasTag('kernel.event_subscriber'));

        $jsonResponseDef = $container->getDefinition('siganushka_generic.listener.json_response');
        static::assertTrue($jsonResponseDef->hasTag('kernel.event_subscriber'));

        $formErrorDef = $container->getDefinition('siganushka_generic.listener.form_error');
        static::assertTrue($formErrorDef->hasTag('kernel.event_subscriber'));

        $resizeImageDef = $container->getDefinition('siganushka_generic.listener.resize_image');
        static::assertTrue($resizeImageDef->hasTag('kernel.event_subscriber'));

        $listenerTagAttributes = [
            ['event' => 'prePersist'],
            ['event' => 'preUpdate'],
        ];

        $timestampableDef = $container->getDefinition('siganushka_generic.doctrine.listener.timestampable');
        static::assertSame(TimestampableListener::class, $timestampableDef->getClass());
        static::assertSame($listenerTagAttributes, $timestampableDef->getTag('doctrine.event_listener'));

        $sortableDef = $container->getDefinition('siganushka_generic.doctrine.listener.sortable');
        static::assertSame(SortableListener::class, $sortableDef->getClass());
        static::assertSame($listenerTagAttributes, $sortableDef->getTag('doctrine.event_listener'));

        $disableHtml5ValidationDef = $container->getDefinition('siganushka_generic.form.type_extension.disable_html5_validation');
        static::assertTrue($disableHtml5ValidationDef->hasTag('form.type_extension'));

        $disableHtml5ValidationDef = $container->getDefinition('siganushka_generic.serializer.normalizer.knp_pagination');
        static::assertTrue($disableHtml5ValidationDef->hasTag('serializer.normalizer'));
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'doctrine' => [
                'table_prefix' => 'test_',
                'entity_to_superclass' => ['foo', 'bar', 'baz'],
            ],
            'form' => [
                'html5_validation' => true,
            ],
        ];

        $container = $this->createContainerWithConfig($configs);

        $entityToSuperclassDef = $container->getDefinition('siganushka_generic.doctrine.listener.entity_to_superclass');
        static::assertSame(EntityToSuperclassListener::class, $entityToSuperclassDef->getClass());
        static::assertSame([['event' => 'loadClassMetadata']], $entityToSuperclassDef->getTag('doctrine.event_listener'));
        static::assertSame($configs['doctrine']['entity_to_superclass'], $entityToSuperclassDef->getArgument(0));

        $tablePrefixDef = $container->getDefinition('siganushka_generic.doctrine.listener.table_prefix');
        static::assertSame(TablePrefixListener::class, $tablePrefixDef->getClass());
        static::assertSame([['event' => 'loadClassMetadata']], $tablePrefixDef->getTag('doctrine.event_listener'));
        static::assertSame($configs['doctrine']['table_prefix'], $tablePrefixDef->getArgument(0));

        static::assertFalse($container->hasDefinition('siganushka_generic.form.type_extension.disable_html5_validation'));
    }

    private function createContainerWithConfig(array $config = []): ContainerBuilder
    {
        $extension = new SiganushkaGenericExtension();

        $container = new ContainerBuilder();
        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias(), $config);

        $container->getCompilerPassConfig()->setOptimizationPasses([new ResolveChildDefinitionsPass()]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
