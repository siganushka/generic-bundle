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

        static::assertNull($container->getParameter('siganushka_generic.doctrine.table_prefix'));
        static::assertSame('Y-m-d H:i:s', $container->getParameter('siganushka_generic.datetime.format'));
        static::assertNull($container->getParameter('siganushka_generic.datetime.timezone'));
        static::assertSame(271, $container->getParameter('siganushka_generic.json.encoding_options'));
        static::assertSame(2, $container->getParameter('siganushka_generic.currency.scale'));
        static::assertTrue($container->getParameter('siganushka_generic.currency.grouping'));
        static::assertSame(\NumberFormatter::ROUND_HALFUP, $container->getParameter('siganushka_generic.currency.rounding_mode'));
        static::assertSame(100, $container->getParameter('siganushka_generic.currency.divisor'));

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

        static::assertSame('test_', $container->getParameter('siganushka_generic.doctrine.table_prefix'));

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
