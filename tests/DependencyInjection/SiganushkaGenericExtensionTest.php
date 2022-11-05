<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\Contracts\Doctrine\EventListener\SortableListener;
use Siganushka\Contracts\Doctrine\EventListener\TablePrefixListener;
use Siganushka\Contracts\Doctrine\EventListener\TimestampableListener;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Doctrine\EventListener\EntityToSuperclassListener;
use Siganushka\GenericBundle\Identifier\SequenceGenerator;
use Siganushka\GenericBundle\Utils\CurrencyUtils;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainerWithConfig([]);

        static::assertTrue($container->hasDefinition('siganushka_generic.listener.json_response'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.public_file'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.resize_image'));
        static::assertTrue($container->hasDefinition('siganushka_generic.identifier.sequence'));
        static::assertTrue($container->hasDefinition('siganushka_generic.utils.public_file'));
        static::assertTrue($container->hasDefinition('siganushka_generic.utils.currency'));
        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.entity_to_superclass'));
        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.table_prefix'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.timestampable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.sortable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.form.type_extension.disable_html5_validation'));
        static::assertTrue($container->hasDefinition('siganushka_generic.serializer.normalizer.translatable'));

        static::assertTrue($container->hasAlias(SequenceGenerator::class));
        static::assertTrue($container->hasAlias(PublicFileUtils::class));
        static::assertTrue($container->hasAlias(CurrencyUtils::class));

        $jsonResponseDef = $container->getDefinition('siganushka_generic.listener.json_response');
        static::assertTrue($jsonResponseDef->hasTag('kernel.event_subscriber'));

        $publicFileDef = $container->getDefinition('siganushka_generic.listener.public_file');
        static::assertTrue($publicFileDef->hasTag('kernel.event_subscriber'));
        static::assertSame('siganushka_generic.utils.public_file', (string) $publicFileDef->getArgument(0));

        $resizeImageDef = $container->getDefinition('siganushka_generic.listener.resize_image');
        static::assertTrue($resizeImageDef->hasTag('kernel.event_subscriber'));

        $currencyDef = $container->getDefinition('siganushka_generic.utils.currency');
        static::assertSame([
            'divisor' => 100,
            'decimals' => 2,
            'dec_point' => '.',
            'thousands_sep' => ',',
        ], $currencyDef->getArgument(0));

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

        $translatableNormalizerDef = $container->getDefinition('siganushka_generic.serializer.normalizer.translatable');
        static::assertTrue($translatableNormalizerDef->hasTag('serializer.normalizer'));
        static::assertSame('translator', (string) $translatableNormalizerDef->getArgument(0));
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
            'currency' => [
                CurrencyUtils::DIVISOR => 1,
                CurrencyUtils::DECIMALS => 0,
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

        $currencyDef = $container->getDefinition('siganushka_generic.utils.currency');
        static::assertSame([
            'divisor' => 1,
            'decimals' => 0,
            'dec_point' => '.',
            'thousands_sep' => ',',
        ], $currencyDef->getArgument(0));

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
