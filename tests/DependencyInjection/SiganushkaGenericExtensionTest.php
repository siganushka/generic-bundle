<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Siganushka\Contracts\Doctrine\EventListener\SortableListener;
use Siganushka\Contracts\Doctrine\EventListener\TablePrefixListener;
use Siganushka\Contracts\Doctrine\EventListener\TimestampableListener;
use Siganushka\GenericBundle\DependencyInjection\SiganushkaGenericExtension;
use Siganushka\GenericBundle\Identifier\SequenceGenerator;
use Siganushka\GenericBundle\Utils\CurrencyUtils;
use Siganushka\GenericBundle\Utils\PublicFileUtils;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncode;

final class SiganushkaGenericExtensionTest extends TestCase
{
    public function testLoadDefaultConfig(): void
    {
        $container = $this->createContainerWithConfigs([]);

        static::assertFalse($container->hasDefinition('siganushka_generic.doctrine.listener.table_prefix'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.timestampable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.doctrine.listener.sortable'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.json_response'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.public_file_data'));
        static::assertTrue($container->hasDefinition('siganushka_generic.listener.resize_image'));
        static::assertTrue($container->hasDefinition('siganushka_generic.serializer.encoder.json'));

        static::assertTrue($container->hasDefinition('siganushka_generic.identifier.generator.sequence'));
        static::assertTrue($container->hasAlias(SequenceGenerator::class));

        static::assertTrue($container->hasDefinition('siganushka_generic.utils.public_file'));
        static::assertTrue($container->hasAlias(PublicFileUtils::class));

        static::assertTrue($container->hasDefinition('siganushka_generic.utils.currency'));
        static::assertTrue($container->hasAlias(CurrencyUtils::class));

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

        $jsonResponseDef = $container->getDefinition('siganushka_generic.listener.json_response');
        static::assertTrue($jsonResponseDef->hasTag('kernel.event_subscriber'));
        static::assertSame(271, $jsonResponseDef->getArgument(0));

        $publicFileDataDef = $container->getDefinition('siganushka_generic.listener.public_file_data');
        static::assertTrue($publicFileDataDef->hasTag('kernel.event_subscriber'));
        static::assertSame('siganushka_generic.utils.public_file', (string) $publicFileDataDef->getArgument(0));

        $resizeImageDef = $container->getDefinition('siganushka_generic.listener.resize_image');
        static::assertTrue($resizeImageDef->hasTag('kernel.event_subscriber'));

        $currencyDef = $container->getDefinition('siganushka_generic.utils.currency');
        static::assertSame([2, '.', ',', 100], $currencyDef->getArguments());

        $jsonEncoderDef = $container->getDefinition('siganushka_generic.serializer.encoder.json');
        static::assertTrue($jsonEncoderDef->hasTag('serializer.encoder'));
        static::assertSame([JsonEncode::OPTIONS => 271], $jsonEncoderDef->getArgument(0)->getArgument(0));
    }

    public function testWithConfigs(): void
    {
        $configs = [
            'doctrine' => [
                'table_prefix' => 'test_',
            ],
            'json' => [
                'encoding_options' => 0,
            ],
            'currency' => [
                'decimals' => 0,
                'divisor' => 1,
            ],
        ];

        $container = $this->createContainerWithConfigs([$configs]);

        $tablePrefixDef = $container->getDefinition('siganushka_generic.doctrine.listener.table_prefix');
        static::assertSame(TablePrefixListener::class, $tablePrefixDef->getClass());
        static::assertSame([['event' => 'loadClassMetadata']], $tablePrefixDef->getTag('doctrine.event_listener'));
        static::assertSame($configs['doctrine']['table_prefix'], $tablePrefixDef->getArgument(0));

        $currencyDef = $container->getDefinition('siganushka_generic.utils.currency');
        static::assertSame([0, '.', ',', 1], $currencyDef->getArguments());

        $jsonEncoderDef = $container->getDefinition('siganushka_generic.serializer.encoder.json');
        static::assertTrue($jsonEncoderDef->hasTag('serializer.encoder'));
        static::assertSame([JsonEncode::OPTIONS => 0], $jsonEncoderDef->getArgument(0)->getArgument(0));
    }

    /**
     * @param array<mixed> $configs
     */
    protected function createContainerWithConfigs(array $configs): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $extension = new SiganushkaGenericExtension();
        $extension->load($configs, $container);

        return $container;
    }
}
