<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Doctrine\EventListener\SortableListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TablePrefixListener;
use Siganushka\GenericBundle\Doctrine\EventListener\TimestampableListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 * @coversNothing
 */
final class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainerWithConfigs([]);

        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.table_prefix'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.timestampable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.sortable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.json_response'));
        static::assertTrue($container->hasDefinition('siganushka_generic.utils.currency'));
        static::assertTrue($container->hasDefinition('siganushka_generic.serializer.encoder.json'));
        static::assertTrue($container->hasDefinition('siganushka_generic.serializer.normalizer.datetime'));

        $listenerTagAttributes = [
            ['event' => 'prePersist'],
            ['event' => 'preUpdate'],
        ];

        $timestampableDef = $container->getDefinition('siganushka_generic.doctrine.listener.timestampable');
        $sortableDef = $container->getDefinition('siganushka_generic.doctrine.listener.sortable');

        static::assertSame(TimestampableListener::class, $timestampableDef->getClass());
        static::assertSame(SortableListener::class, $sortableDef->getClass());
        static::assertSame($listenerTagAttributes, $timestampableDef->getTag('doctrine.event_listener'));
        static::assertSame($listenerTagAttributes, $sortableDef->getTag('doctrine.event_listener'));
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'doctrine' => [
                'table_prefix' => 'test_',
            ],
        ];

        $container = $this->createContainerWithConfigs([$configs]);

        $tablePrefixDef = $container->getDefinition('siganushka_generic.doctrine.listener.table_prefix');

        static::assertSame(TablePrefixListener::class, $tablePrefixDef->getClass());
        static::assertSame([['event' => 'loadClassMetadata']], $tablePrefixDef->getTag('doctrine.event_listener'));
    }

    protected function createContainerWithConfigs(array $configs): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $extension = new SiganushkaGenericExtension();
        $extension->load($configs, $container);

        return $container;
    }
}
